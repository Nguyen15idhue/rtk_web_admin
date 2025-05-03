<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\purchase\process_transaction_approve.php
declare(strict_types=1);
header('Content-Type: application/json'); 
// --- Prerequisites ---
// Ensure session started, user is admin, CSRF protection is in place etc.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // Ensure session is started to read the cookie
}
// if (!isset($_SESSION['admin_id']) || !check_admin_permission('transaction_approve')) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Permission denied.']);
//     exit;
// }
// if (!verify_csrf_token($_POST['csrf_token'] ?? '')) { // Example CSRF check
//     http_response_code(400);
//     echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
//     exit;
// }

// Load our path constants
require_once __DIR__ . '/../../config/constants.php';

// Replace hard‑coded relative paths with BASE_PATH
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/classes/Database.php';
// require_once BASE_PATH . '/utils/logger.php'; // Example: For logging actions
// require_once BASE_PATH . '/utils/permissions.php'; // Example: For permission checks
// require_once BASE_PATH . '/services/SurveyAccountService.php'; // Example: Service to activate accounts
require_once BASE_PATH . '/services/TransactionHistoryService.php'; // Service to manage transaction history
require_once BASE_PATH . '/api/rtk_system/account_api.php';      // thêm
require_once BASE_PATH . '/utils/functions.php';                // thêm (generate_unique_id,…)

// // --- Input Validation ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Expecting JSON payload
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}
$transaction_id = filter_var($input['transaction_id'] ?? null, FILTER_VALIDATE_INT);

if ($transaction_id === false || $transaction_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid or missing transaction ID.']);
    exit;
}

// NEW: Initialize DB connection before history lookup
$database = Database::getInstance();
$db       = $database->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Database connection failed.']);
    exit;
}

// NEW: treat $transaction_id as history ID, fetch the real registration ID and transaction type
$stmt_hist = $db->prepare("
    SELECT registration_id, transaction_type 
    FROM transaction_history 
    WHERE id = :history_id
");
$stmt_hist->bindParam(':history_id', $transaction_id, PDO::PARAM_INT);
$stmt_hist->execute();
$hist = $stmt_hist->fetch(PDO::FETCH_ASSOC);
if (!$hist) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Transaction history not found.']);
    exit;
}
$registration_id = (int)$hist['registration_id'];
$tx_type         = $hist['transaction_type']; 

// --- Processing ---
$db->beginTransaction();

try {
    // 1. Verify Transaction Exists and is Pending or Rejected (Allow re-approval)
    $stmt_check = $db->prepare("
        SELECT status,
               user_id,
               num_account,
               start_time,
               end_time,
               location_id,
               package_id           -- added
        FROM registration
        WHERE id = :reg_id AND deleted_at IS NULL
        FOR UPDATE
    ");
    $stmt_check->bindParam(':reg_id', $registration_id, PDO::PARAM_INT);
    $stmt_check->execute();
    error_log("[PTA] check SQL executed, rowCount=".$stmt_check->rowCount());
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);
    error_log("[PTA] fetched registration=".json_encode($registration));

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    // Allow approving if pending or previously rejected, except for renewals
    if ($tx_type !== 'renewal' && !in_array($registration['status'], ['pending', 'rejected'])) {
        throw new Exception(
            "Transaction cannot be approved (Current Status: {$registration['status']}). " .
            "Only pending or rejected transactions can be approved."
        );
    }
    $old_status = $registration['status']; // Store old status for logging

    // fetch user email for create_account payload
    $stmt_email = $db->prepare("SELECT email FROM `user` WHERE id = :uid");
    $stmt_email->bindParam(':uid', $registration['user_id'], PDO::PARAM_INT);
    $stmt_email->execute();
    $userEmail = $stmt_email->fetchColumn();

    // Determine duration based on original start/end timestamps
    $tz = new DateTimeZone('Asia/Bangkok'); // GMT+7
    $origStartTs = strtotime($registration['start_time']);
    $origEndTs   = strtotime($registration['end_time']);
    $durationSec = max(0, $origEndTs - $origStartTs);

    // Calculate new start and end times based on approval time
    $new_start_time_dt = new DateTime('now', $tz);
    $new_end_time_dt   = clone $new_start_time_dt;
    $new_end_time_dt->modify("+{$durationSec} seconds");

    $new_start_time_sql = $new_start_time_dt->format('Y-m-d H:i:s');
    $new_end_time_sql   = $new_end_time_dt->format('Y-m-d H:i:s');

    // 2. Update Registration Status, clear rejection reason, reset start/end times
    $stmt_update_reg = $db->prepare("
        UPDATE registration 
        SET status = 'active',
            rejection_reason = NULL,
            start_time = :new_start,
            end_time   = :new_end,
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmt_update_reg->bindParam(':new_start', $new_start_time_sql);
    $stmt_update_reg->bindParam(':new_end',   $new_end_time_sql);
    $stmt_update_reg->bindParam(':id',        $registration_id, PDO::PARAM_INT);
    $updated_reg = $stmt_update_reg->execute();
    error_log("[PTA] update registration executed, affectedRows=".$stmt_update_reg->rowCount());
    if (!$updated_reg) {
        throw new Exception("Failed to update registration status and times.");
    }

    // 3. Update Payment Confirmation (if applicable)
    $stmt_update_pay = $db->prepare("UPDATE payment SET confirmed = 1, confirmed_at = NOW(), updated_at = NOW() WHERE registration_id = :id");
    $stmt_update_pay->bindParam(':id', $registration_id, PDO::PARAM_INT);
    $stmt_update_pay->execute();
    error_log("[PTA] update payment executed, affectedRows=".$stmt_update_pay->rowCount());

    // 4. Activate Associated Survey Account(s)
    $stmt_activate_acc = $db->prepare("UPDATE survey_account SET enabled = 1, updated_at = NOW() WHERE registration_id = :id AND deleted_at IS NULL");
    $stmt_activate_acc->bindParam(':id', $registration_id, PDO::PARAM_INT);
    $activated_acc = $stmt_activate_acc->execute();
    error_log("[PTA] activate survey_account executed, affectedRows=".$stmt_activate_acc->rowCount());
    $accounts_activated_count = $stmt_activate_acc->rowCount();

    if ($accounts_activated_count == 0) {
        error_log("Warning: No survey accounts found or enabled for approved registration ID: " . $registration_id);
    }

    // --- NEW: if this is a renewal, only update account times ---
    if ($tx_type === 'renewal') {
        // --- Lấy duration_text của gói ---
        $stmt_pkg = $db->prepare("SELECT duration_text FROM package WHERE id = :pid");
        $stmt_pkg->bindParam(':pid', $registration['package_id'], PDO::PARAM_INT);
        $stmt_pkg->execute();
        $durationText = $stmt_pkg->fetchColumn();

        // --- Parse duration_text sang giây ---
        $durationSecPackage = 0;
        if (preg_match('/(\d+)\s*ngày/',   $durationText, $m1)) {
            $durationSecPackage = intval($m1[1]) * 86400;
        } elseif (preg_match('/(\d+)\s*tháng/',$durationText, $m2)) {
            $durationSecPackage = intval($m2[1]) * 30 * 86400;
        } elseif (preg_match('/(\d+)\s*năm/',$durationText, $m3)) {
            $durationSecPackage = intval($m3[1]) * 365 * 86400;
        }
        $durationMs = $durationSecPackage * 1000;

        // Thời điểm hiện tại (ms)
        $tz    = new DateTimeZone('Asia/Bangkok');
        $nowMs = (new DateTime('now', $tz))->getTimestamp() * 1000;

        // Tính start/end mới dựa trên now
        $newStartMs = max($origEndTs * 1000, $nowMs);
        $newEndMs   = $newStartMs + $durationMs;

        // --- Cập nhật lại start_time và end_time của registration ---
        $stmt_update_reg = $db->prepare("
            UPDATE registration
            SET start_time = FROM_UNIXTIME(:nowMs/1000),
                end_time   = FROM_UNIXTIME(:endMs/1000),
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt_update_reg->bindParam(':startMs', $newStartMs, PDO::PARAM_INT);
        $stmt_update_reg->bindParam(':endMs',   $newEndMs,   PDO::PARAM_INT);
        $stmt_update_reg->bindParam(':id',      $registration_id, PDO::PARAM_INT);
        $stmt_update_reg->execute();
        error_log("[PTA] registration(id={$registration_id}) times updated for renewal");

        // tính độ dài gốc (ms)
        $durationMs   = $durationSec * 1000;
        // end_time gốc từ registration (ms)
        $origEndMs    = $origEndTs * 1000;
        // thời điểm hiện tại (ms)
        $nowMs        = (new DateTime('now', $tz))->getTimestamp() * 1000;

        // NEW: Lấy mountIds cho location
        $mountIds = getMountPointsByLocationId($registration['location_id']);

        // NEW: Lấy thông tin các survey_account (id, username, password, concurrent_user)
        $stmt_accs = $db->prepare("
            SELECT id, username_acc, password_acc, concurrent_user
            FROM survey_account
            WHERE registration_id = :id AND deleted_at IS NULL
        ");
        $stmt_accs->bindParam(':id', $registration_id, PDO::PARAM_INT);
        $stmt_accs->execute();
        $accounts = $stmt_accs->fetchAll(PDO::FETCH_ASSOC);

        foreach ($accounts as $acc) {
            // khởi tạo lại thời điểm start/end mỗi account
            $newStartMs = max($origEndMs, $nowMs);
            $newEndMs   = $newStartMs + $durationMs;

            // Build payload giống toggle_account_status.php
            $payload = [
                'id'              => $acc['id'],
                'name'            => $acc['username_acc'],
                'userPwd'         => $acc['password_acc'],
                'startTime'       => $nowMs,
                'endTime'         => $newEndMs,
                'enabled'         => 1,
                'numOnline'       => (int)$acc['concurrent_user'],
                'customerBizType' => 1,
                'mountIds'        => $mountIds
            ];

            // Gọi API cập nhật
            updateRtkAccount($payload);
            error_log("[PTA] renewal update for acc {$acc['id']} with payload: ".json_encode($payload));
        }

        // NEW: mark this history record as completed
        $stmt_hist_up = $db->prepare("
            UPDATE transaction_history 
            SET status = 'completed', updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt_hist_up->bindParam(':id', $transaction_id, PDO::PARAM_INT);
        $stmt_hist_up->execute();
        error_log("[PTA] transaction_history id={$transaction_id} marked completed for renewal");

        // commit và kết thúc sớm
        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => "Transaction #{$transaction_id} renewed; account times updated."
        ]);
        exit;
    }

    // 5. Tạo account qua endpoint create_account.php
    // build dynamic URL to include correct host & port
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host     = $_SERVER['SERVER_NAME'];
    $port     = $_SERVER['SERVER_PORT'] ?? '';
    if ($port && !in_array($port, ['80','443'], true)) {
        $host .= ':' . $port;
    }
    // derive base path (e.g. '/actions') from current script
    $script   = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname(dirname($script));
    $url      = "{$protocol}://{$host}{$basePath}/account/index.php?action=create_account";

    // Generate username = province_code + next 3-digit sequence
    $stmt_province = $db->prepare("SELECT province_code FROM location WHERE id = :loc");
    $stmt_province->bindParam(':loc', $registration['location_id'], PDO::PARAM_INT);
    $stmt_province->execute();
    $province_code = $stmt_province->fetchColumn();
    $stmt_last = $db->prepare("SELECT username_acc FROM survey_account WHERE username_acc LIKE ? ORDER BY username_acc DESC LIMIT 1");
    $stmt_last->execute([$province_code . '%']);
    $lastUser = $stmt_last->fetchColumn();
    $nextSeq = 1;
    if ($lastUser && preg_match('/^' . preg_quote($province_code,'/') . '(\d{3})$/', $lastUser, $m)) {
        $nextSeq = intval($m[1]) + 1;
    }
    $username = sprintf('%s%03d', $province_code, $nextSeq);
    // Password is the user's phone
    $stmt_user = $db->prepare("SELECT phone FROM user WHERE id = :uid");
    $stmt_user->bindParam(':uid', $registration['user_id'], PDO::PARAM_INT);
    $stmt_user->execute();
    $password = $stmt_user->fetchColumn();

    $payload = [
        'registration_id' => $registration_id,
        'username_acc'    => $username,
        'password_acc'    => $password,
        'user_email'      => $userEmail,                
        'location_id'     => $registration['location_id'], 
        'package_id'      => $registration['package_id'],  
        'start_time'      => $new_start_time_sql,
        'end_time'        => $new_end_time_sql,
        'enabled'         => 1,
        'concurrent_user' => 1,
        'account_count'   => (int)$registration['num_account'],
    ];

    error_log("[PTA] province_code={$province_code}, lastUser={$lastUser}, nextSeq={$nextSeq}, username={$username}, password={$password}");
    error_log("[PTA] cURL timeout set to 1s; calling public front-controller URL={$url} with payload=".json_encode($payload));

    // --- Release session lock before internal request ---
    // Preserve needed session data before closing
    $adminId = $_SESSION['admin_id'] ?? null;
    session_write_close(); 
    // --- End Release session lock ---

    // call internal script
    $accountCreationFailedDueToTimeout = false; // Flag for timeout
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json; charset=utf-8',
        'User-Agent: PHP-cURL'      
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // reduced connect timeout to 1s
    curl_setopt($ch, CURLOPT_TIMEOUT,        1); // reduced execution timeout to 1s
    // disable SSL verification for self-signed certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if (isset($_COOKIE[session_name()])) {
        curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
    }

    // DEBUG: log request URL and payload
    error_log("DEBUG [Account Creation] URL: {$url}");
    error_log("DEBUG [Account Creation] Payload: " . json_encode($payload));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch); // Get the cURL error number
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME); // Get total time taken
    curl_close($ch);

    $error_log_data = [
        'httpCode'=>$httpCode,'errno'=>$curlErrno,'error'=>$curlError,'totalTime'=>$totalTime
    ];
    error_log("[PTA] cURL finished: ".json_encode($error_log_data));

    // --- Refined cURL error handling ---
    if ($curlErrno === CURLE_OPERATION_TIMEDOUT) {
        // It's possible the timeout happened but the account was still created.
        // Log this specific case for manual verification.
        error_log("[PTA] cURL request timed out for transaction ID {$transaction_id}. Account creation status uncertain. Please verify manually.");
        $accountCreationFailedDueToTimeout = true; // Keep the flag for the response message
        // Do NOT throw an exception here if you want the DB transaction to commit.
        // The registration is approved, only the account creation via API timed out.
        // Set $createdAccounts to an empty array or null as we don't have confirmation.
        $createdAccounts = [];
    } elseif ($curlError) {
        throw new Exception("Account creation request failed (cURL Error): {$curlError}");
    } elseif ($httpCode !== 200) {
        // Log the response body if available for non-200 responses
        $errorDetails = $result ? " Response: " . substr($result, 0, 500) : ""; // Limit response length in log
        error_log("[PTA] Account creation failed (HTTP Status {$httpCode}) for transaction ID {$transaction_id}.{$errorDetails}");
        throw new Exception("Account creation failed (HTTP Status {$httpCode})");
    } else {
        // successful → parse & validate JSON
        $resData = json_decode($result, true);
        if (!is_array($resData) || empty($resData['success'])) {
            throw new Exception("Account creation failed: ".($resData['message'] ?? 'Invalid response'));
        }
        if (!empty($resData['accounts']) && is_array($resData['accounts'])) {
            $createdAccounts = $resData['accounts'];
        } elseif (!empty($resData['account'])) {
            $createdAccounts = [ $resData['account'] ];
        } else {
            // If success is true but no account data, maybe it's okay? Log a warning.
            error_log("[PTA] Warning: Account creation reported success but returned no account data for transaction ID {$transaction_id}. Response: " . $result);
            $createdAccounts = []; // Assume none created if data is missing
            // OR: throw new Exception("Account creation response missing account data.");
        }
    }

    // 6. Update Transaction History
    TransactionHistoryService::updateStatusByRegistrationId($db, $registration_id, 'completed');
    error_log("[PTA] TransactionHistoryService updated");

    // 7. Log Activity
    // log_admin_activity($adminId, 'approve_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'active']); // Example

    // 8. TODO: Send Notifications (Email/SMS to user?)

    // --- Commit Transaction ---
    $db->commit();
    error_log("[PTA] Transaction committed successfully");

    // --- Prepare final response ---
    $responseMessage = 'Transaction #' . $transaction_id . ' approved.';
    if (!empty($createdAccounts)) {
        $count = count($createdAccounts);
        $responseMessage .= " {$count} account(s) created successfully.";
    }

    echo json_encode([
        'success' => true,
        'message' => $responseMessage,
        'accounts' => $createdAccounts ?? [] // Ensure accounts is always an array
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error approving transaction ID $transaction_id: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());          // <-- Added detailed stack trace
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to approve transaction: ' . $e->getMessage()]);
} finally {
    $database->close();
}

exit;
?>