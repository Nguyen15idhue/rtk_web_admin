<?php
declare(strict_types=1);
header('Content-Type: application/json');


$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db        = $bootstrap['db'];

if (!defined('IS_CRON')) {
    Auth::ensureAuthorized('invoice_management_edit');
}

// Capture logged-in admin ID
$sessionAdminId = $_SESSION['admin_id'] ?? null;

/**
 * Returns a valid admin user ID if it exists in the 'user' table, or null.
 */
function getValidAdminId(PDO $db, $adminId): ?int {
    if (is_numeric($adminId) && (int)$adminId > 0) {
        $stmt = $db->prepare("SELECT id FROM user WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn()) {
            return (int)$adminId;
        }
    }
    return null;
}

// Load any additional services you still need
require_once BASE_PATH . '/services/TransactionHistoryService.php';
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/classes/TransactionModel.php';
require_once BASE_PATH . '/classes/ActivityLogModel.php'; // Added for ActivityLogModel

// --- Input Validation ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
}

// Expecting JSON payload
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}
$transaction_id = filter_var($input['transaction_id'] ?? null, FILTER_VALIDATE_INT);

if ($transaction_id === false || $transaction_id <= 0) {
    api_error('Invalid or missing transaction ID.', 400);
}

// --- Processing ---
$db->beginTransaction();
$tm = new TransactionModel();

// fetch history → reg_id + type
$th = $tm->getHistoryById($transaction_id);
if (!$th) api_error('Not found',404);
$registration_id = (int)$th['registration_id'];
$tx_type = $th['transaction_type'];

$reg = $tm->getRegistrationById($registration_id);
if (!$reg) throw new Exception("Not found");

// Determine duration based on original start/end timestamps
$tz = new DateTimeZone('Asia/Bangkok'); // GMT+7
$origStartTs = strtotime($reg['start_time']);
$origEndTs   = strtotime($reg['end_time']);
$durationSec = max(0, $origEndTs - $origStartTs);

// Calculate new start and end times based on approval time
$new_start_time_dt = new DateTime('now', $tz);
$new_end_time_dt   = clone $new_start_time_dt;
$new_end_time_dt->modify("+{$durationSec} seconds");

$new_start_time_sql = $new_start_time_dt->format('Y-m-d H:i:s');
$new_end_time_sql   = $new_end_time_dt->format('Y-m-d H:i:s');

$tm->updateRegistrationStatus($registration_id, 'active');
$tm->unconfirmPayment($transaction_id); // or confirmPayment if you add method
$tm->updateHistoryStatus($transaction_id, 'completed');

// fetch user email for create_account payload
$stmt_email = $db->prepare("SELECT email FROM `user` WHERE id = :uid");
$stmt_email->bindParam(':uid', $reg['user_id'], PDO::PARAM_INT);
$stmt_email->execute();
$userEmail = $stmt_email->fetchColumn();

// 4. Activate Associated Survey Account(s)
$stmt_activate_acc = $db->prepare("
    UPDATE survey_account 
    SET enabled    = 1,
        start_time = :new_start,
        end_time   = :new_end,
        updated_at = NOW() 
    WHERE registration_id = :id AND deleted_at IS NULL
");
$stmt_activate_acc->bindParam(':new_start', $new_start_time_sql);
$stmt_activate_acc->bindParam(':new_end',   $new_end_time_sql);
$stmt_activate_acc->bindParam(':id',        $registration_id, PDO::PARAM_INT);
$activated_acc = $stmt_activate_acc->execute();
error_log("[PTA] activate survey_account executed, affectedRows=".$stmt_activate_acc->rowCount());
$accounts_activated_count = $stmt_activate_acc->rowCount();

if ($accounts_activated_count == 0) {
    error_log("Warning: No survey accounts found or enabled for approved registration ID: " . $registration_id);
}

// --- NEW: delegate renewals to update_account endpoint with per-account start/end times ---
if ($tx_type === 'renewal') {
    // fetch all account IDs under this registration
    $stmt_acc = $db->prepare("
        SELECT survey_account_id AS id
        FROM account_groups
        WHERE registration_id = :id
    ");
    $stmt_acc->bindParam(':id', $registration_id, PDO::PARAM_INT);
    $stmt_acc->execute();
    $accIds = $stmt_acc->fetchAll(PDO::FETCH_COLUMN);

    $stmtUpdReg = $db->prepare("
        UPDATE survey_account
        SET registration_id = :newRegId, updated_at = NOW()
        WHERE id = :aid
    ");
    foreach ($accIds as $aid) {
        $stmtUpdReg->execute([
            ':newRegId' => $registration_id,
            ':aid'      => $aid
        ]);
    }
    error_log("[PTA] Updated registration_id to {$registration_id} for accounts: " . implode(',', $accIds));

    $renewed = [];
    // compute registration duration once
    $durationSec = max(0, $origEndTs - $origStartTs);

    // NEW: log which accounts will be renewed
    error_log("[PTA] Accounts slated for renewal: " . implode(',', $accIds));

    // NEW: Fetch all account details in batch before the loop
    $accountsDetailsMap = [];
    if (!empty($accIds)) {
        $placeholdersAccDetails = implode(',', array_fill(0, count($accIds), '?'));
        $sqlAccDetails = "SELECT id, end_time, username_acc, password_acc FROM survey_account WHERE id IN ({$placeholdersAccDetails})";
        $stmtAccDetails = $db->prepare($sqlAccDetails);
        try {
            $stmtAccDetails->execute(array_values($accIds)); // Ensure $accIds is numerically indexed
            $allAccountDetails = $stmtAccDetails->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allAccountDetails as $detail) {
                $accountsDetailsMap[$detail['id']] = $detail;
            }
        } catch (PDOException $e) {
            error_log("[PTA] Failed to fetch batch account details for renewal: " . $e->getMessage());
            // Depending on policy, might throw an exception or allow proceeding with partial data / defaults
        }
    }

    // NEW: preserve session name & ID before releasing lock
    $sessName = session_name();
    $sessId   = session_id();
    session_write_close();

    // NEW: commit transaction to release DB locks before cURL calls for account renewals
    $db->commit();

    foreach ($accIds as $aid) {
        // Get account details from the pre-fetched map
        $accountDetail = $accountsDetailsMap[$aid] ?? null;

        if (!$accountDetail) {
            error_log("[PTA] Renewal SKIPPED for account {$aid}: Details not found (possibly due to earlier fetch error or account deleted).");
            continue; // Skip this account if details are missing
        }

        $oldEnd = $accountDetail['end_time'];
        $usernameAcc = $accountDetail['username_acc'];
        $passwordAcc = $accountDetail['password_acc'];

        $oldEndTs = strtotime($oldEnd);
        $nowTs    = time();

        // per-account new start/end: always start from now
        $startTs  = $nowTs;
        $newStart = date('Y-m-d H:i:s', $startTs); 
        $newEnd   = date('Y-m-d H:i:s', $startTs + $durationSec);

        // build internal URL to our update_account API
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on') ? 'https' : 'http';
        $host     = $_SERVER['SERVER_NAME'] . (isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80','443'], true) ? ':' . $_SERVER['SERVER_PORT'] : '');
        $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
        $updateUrl = "{$protocol}://{$host}{$basePath}/account/index.php?action=update_account";

        // call update_account with full payload
        $payload = [
            'id'              => $aid,
            'username_acc'    => $usernameAcc,
            'password_acc'    => $passwordAcc,
            'status'          => 'active',
            'activation_date' => substr($newStart, 0, 10),
            'expiry_date'     => substr($newEnd,   0, 10),
        ];
        $ch = curl_init($updateUrl);
        curl_setopt($ch, CURLOPT_POST,            true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,  true);
        // apply timeout and SSL options
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,  1); 
        curl_setopt($ch, CURLOPT_TIMEOUT,         1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'User-Agent: PHP-cURL'
        ]);
        error_log("[PTA] renewing account {$aid} with cookie {$sessName}={$sessId}");
        curl_setopt($ch, CURLOPT_COOKIE, "{$sessName}={$sessId}");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $resp      = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        // IMPROVED LOGGING: Log result for each account
        error_log("[PTA][{$aid}] HTTP={$httpCode} errno={$curlErrno} resp=" . substr((string)$resp,0,200));
        if ($curlErrno === 0 && $httpCode === 200) {
            $decodedResp = json_decode($resp, true);
            if ($decodedResp['success'] ?? false) {
                $renewed[] = $aid;
                error_log("[PTA] Renewal update SUCCESS for account {$aid}. HTTP {$httpCode}. Response: " . $resp);
            } else {
                error_log("[PTA] Renewal update API returned failure for account {$aid}. HTTP {$httpCode}. Response: " . $resp);
            }
        } else {
            error_log("[PTA] Renewal update cURL FAILED for account {$aid}. HTTP {$httpCode}. cURL Errno: {$curlErrno}. Response: " . $resp);
        }
    }

    // NEW: restore session after cURL calls
    if (session_status() === PHP_SESSION_NONE && $sessId !== '') {
        session_id($sessId);
        session_name($sessName);
        session_start();
    }

    // NEW: log summary of renewals
    error_log("[PTA] Renewal completed for accounts: " . implode(',', $renewed));
    if (count($renewed) < count($accIds)) {
        $failed = array_diff($accIds, $renewed);
        error_log("[PTA] Renewal failed for accounts: " . implode(',', $failed));
    }

    // mark history completed
    $db->beginTransaction();
    $stmt_hist = $db->prepare("
        UPDATE transaction_history
        SET status = 'completed', updated_at = NOW()
        WHERE id = :hid
    ");
    $stmt_hist->bindParam(':hid', $transaction_id, PDO::PARAM_INT);
    $stmt_hist->execute();
    $db->commit();

    // Activity log: record approval for renewal flow
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'     => $reg['user_id'], // Use customerId from registration
            ':action'      => 'approve_transaction',
            ':entity_type' => 'transaction',
            ':entity_id'   => $transaction_id,
            ':old_values'  => json_encode(['status' => $th['status']]),
            ':new_values'  => json_encode([
                'status' => 'completed',
                'scheduled_accounts' => $accIds,
                'renewed_accounts' => $renewed,
                'customer_id' => $reg['user_id'],
            ]),
            ':notify_content' => "Giao dịch #{$transaction_id} (Gia hạn) đã được duyệt. Tài khoản dự kiến: " . count($accIds) . ", Đã gia hạn: " . count($renewed)
        ]
    );

    api_success([
        'scheduled_accounts' => $accIds,
        'renewed_accounts'   => $renewed
    ], "Giao dịch #{$transaction_id} đã được duyệt.");
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

// --- include helper for generating usernames ---
require_once BASE_PATH . '/utils/account_helpers.php';

// Generate username for survey account
$username = generateSurveyAccountUsername($db, (int)$reg['location_id']);

// Password is the user's phone
$stmt_user = $db->prepare("SELECT phone FROM user WHERE id = :uid");
$stmt_user->bindParam(':uid', $reg['user_id'], PDO::PARAM_INT);
$stmt_user->execute();
$password = $stmt_user->fetchColumn();

$payload = [
    'registration_id' => $registration_id,
    'username_acc'    => $username,
    'password_acc'    => $password,
    'user_email'      => $userEmail,                
    'location_id'     => $reg['location_id'], 
    'package_id'      => $reg['package_id'],  
    'start_time'      => $new_start_time_sql,
    'end_time'        => $new_end_time_sql,
    'enabled'         => 1,
    'concurrent_user' => 1,
    'account_count'   => (int)$reg['num_account'],
];

error_log("[PTA] Generated survey account username: {$username}");
error_log("[PTA] cURL timeout set to 5s; calling public front-controller URL={$url} with payload=".json_encode($payload));

// --- Release session lock before internal request ---
// Preserve needed session data before closing
$adminId = $_SESSION['admin_id'] ?? null;
session_write_close(); 
// --- End Release session lock ---

// --- Commit DB trước khi gọi external API ---
TransactionHistoryService::updateStatusByRegistrationId($db, $registration_id, 'completed');
$db->commit();

// Activity log: record approval for account creation flow
ActivityLogModel::addLog(
    $db,
    [
        ':user_id'     => $reg['user_id'], // Use customerId from registration
        ':action'      => 'approve_transaction',
        ':entity_type' => 'transaction',
        ':entity_id'   => $transaction_id,
        ':old_values'  => json_encode(['status' => $th['status']]),
        ':new_values'  => json_encode([
            'status'           => 'completed',
            'created_accounts' => $createdAccounts ?? [],
            'customer_id'      => $reg['user_id'],
        ]),
        ':notify_content' => "Giao dịch #{$transaction_id} (Tạo mới tài khoản) đã được duyệt. Số tài khoản sẽ tạo: " . ($payload['account_count'] ?? 0)
    ]
);

// --- NEW: gọi cURL create_account AFTER commit ---
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json; charset=utf-8',
    'User-Agent: PHP-cURL'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);  
curl_setopt($ch, CURLOPT_TIMEOUT,        8);  
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// Pass current PHP session ID to maintain authentication in internal request
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());


$result    = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno = curl_errno($ch);
curl_close($ch);

// xử lý kết quả
if ($curlErrno === CURLE_OPERATION_TIMEDOUT) {
    error_log("[PTA] create_account timed out (could be pending), transaction {$transaction_id}");
    api_error('Tạo tài khoản thất bại: Kết nối tới máy chủ quá lâu (timeout). Vui lòng thử lại.', 500);
} elseif ($curlErrno || $httpCode !== 200) {
    error_log("[PTA] create_account failed (HTTP {$httpCode}, cURL err {$curlErrno})");
    api_error('Tạo tài khoản thất bại: Lỗi kết nối hoặc máy chủ trả về lỗi. Vui lòng thử lại.', 500);
} else {
    $resData = json_decode($result, true);
    if (!empty($resData['accounts'])) {
        $createdAccounts = $resData['accounts'];
    } elseif (!empty($resData['account'])) {
        $createdAccounts = [ $resData['account'] ];
    } else {
        error_log("[PTA] Warning: create_account returned no data. Resp={$result}");
    }
}

// --- Prepare final response ---
$responseMessage = 'Giao dịch #' . $transaction_id . ' đã được duyệt.';
if (!empty($createdAccounts)) {
    $count = count($createdAccounts);
    $responseMessage .= " {$count} account(s) created successfully.";
}

api_success($createdAccounts ?? [], $responseMessage);
?>