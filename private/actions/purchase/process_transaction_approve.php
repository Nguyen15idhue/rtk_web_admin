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

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
// require_once __DIR__ . '/../../utils/logger.php'; // Example: For logging actions
// require_once __DIR__ . '/../../utils/permissions.php'; // Example: For permission checks
// require_once __DIR__ . '/../../services/SurveyAccountService.php'; // Example: Service to activate accounts
require_once __DIR__ . '/../../services/TransactionHistoryService.php'; // Service to manage transaction history
require_once __DIR__ . '/../../api/rtk_system/account_api.php';      // thêm
require_once __DIR__ . '/../../utils/functions.php';                // thêm (generate_unique_id,…)

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

// --- Processing ---
// Instantiate Database and get connection
$db = (Database::getInstance())->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error approving transaction: Database connection failed.");
    exit;
}

error_log("[PTA] Start approve transaction_id={$transaction_id}");
$db->beginTransaction();
error_log("[PTA] Transaction begun");

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
        WHERE id = :id AND deleted_at IS NULL
        FOR UPDATE
    ");
    $stmt_check->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_check->execute();
    error_log("[PTA] check SQL executed, rowCount=".$stmt_check->rowCount());
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);
    error_log("[PTA] fetched registration=".json_encode($registration));

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    // Allow approving if pending or previously rejected
    if (!in_array($registration['status'], ['pending', 'rejected'])) {
        throw new Exception("Transaction cannot be approved (Current Status: " . $registration['status'] . "). Only pending or rejected transactions can be approved.");
    }
    $old_status = $registration['status']; // Store old status for logging

    // fetch user email for create_account payload
    $stmt_email = $db->prepare("SELECT email FROM `user` WHERE id = :uid");
    $stmt_email->bindParam(':uid', $registration['user_id'], PDO::PARAM_INT);
    $stmt_email->execute();
    $userEmail = $stmt_email->fetchColumn();

    // Calculate original duration in GMT+7
    $tz = new DateTimeZone('Asia/Bangkok'); // GMT+7
    $original_start_time = new DateTime($registration['start_time'], $tz);
    $original_end_time   = new DateTime($registration['end_time'], $tz);
    $duration_interval   = $original_start_time->diff($original_end_time);

    // Calculate new start and end times based on approval time
    $new_start_time_dt = new DateTime('now', $tz);
    $new_end_time_dt   = clone $new_start_time_dt;
    $new_end_time_dt->add($duration_interval);

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
    $stmt_update_reg->bindParam(':id',        $transaction_id, PDO::PARAM_INT);
    $updated_reg = $stmt_update_reg->execute();
    error_log("[PTA] update registration executed, affectedRows=".$stmt_update_reg->rowCount());
    if (!$updated_reg) {
        throw new Exception("Failed to update registration status and times.");
    }

    // 3. Update Payment Confirmation (if applicable)
    $stmt_update_pay = $db->prepare("UPDATE payment SET confirmed = 1, confirmed_at = NOW(), updated_at = NOW() WHERE registration_id = :id");
    $stmt_update_pay->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_update_pay->execute();
    error_log("[PTA] update payment executed, affectedRows=".$stmt_update_pay->rowCount());

    // 4. Activate Associated Survey Account(s)
    $stmt_activate_acc = $db->prepare("UPDATE survey_account SET enabled = 1, updated_at = NOW() WHERE registration_id = :id AND deleted_at IS NULL");
    $stmt_activate_acc->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $activated_acc = $stmt_activate_acc->execute();
    error_log("[PTA] activate survey_account executed, affectedRows=".$stmt_activate_acc->rowCount());
    $accounts_activated_count = $stmt_activate_acc->rowCount();

    // Check if activation was successful (at least one account should be linked usually)
    if ($accounts_activated_count == 0) {
        // Decide handling: Rollback? Log warning? Depends on business logic.
        // For now, log a warning but proceed, assuming registration update is primary.
        error_log("Warning: No survey accounts found or enabled for approved registration ID: " . $transaction_id);
        // OR: throw new Exception("Failed to activate associated survey account(s). Rollback required.");
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
        'registration_id' => $transaction_id,
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
    error_log("[PTA] cURL timeout set to 5s; calling public front-controller URL={$url} with payload=".json_encode($payload));

    // --- Release session lock before internal request ---
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
        'User-Agent: PHP-cURL'       // giả lập User‑Agent browser
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // give more time for connection
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);        // give the script more execution time
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
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'completed');
    error_log("[PTA] TransactionHistoryService updated");

    // 7. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'approve_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'active']); // Example

    // 8. TODO: Send Notifications (Email/SMS to user?)

    // --- Commit Transaction ---
    $db->commit();
    error_log("[PTA] Transaction committed successfully");

    // --- Prepare final response ---
    $responseMessage = 'Transaction #' . $transaction_id . ' approved.';
    // Only report account creation success if the request didn't time out and we have accounts
    if ($accountCreationFailedDueToTimeout) {
        $responseMessage .= ' However, the account creation request timed out. Please verify account status manually.';
    } elseif (!empty($createdAccounts)) {
        $count = count($createdAccounts);
        $responseMessage .= " {$count} account(s) created successfully.";
    } elseif ($httpCode === 200) { // If HTTP was 200 but no accounts returned (and not timeout)
        $responseMessage .= " Account creation request succeeded but returned no account details.";
    }
    // Note: If an exception was thrown earlier due to non-timeout cURL/HTTP errors, this part isn't reached.

    echo json_encode([
        'success' => true, // The DB transaction succeeded, even if account creation timed out
        'message' => $responseMessage,
        'accounts' => $createdAccounts ?? [] // Ensure accounts is always an array
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error approving transaction ID $transaction_id: " . $e->getMessage()); // Log detailed error
    http_response_code(500); // Internal Server Error
    // Provide a generic error message to the client
    echo json_encode(['success' => false, 'message' => 'Failed to approve transaction: ' . $e->getMessage()]);
}

exit;
?>