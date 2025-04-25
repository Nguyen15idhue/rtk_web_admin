<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\purchase\process_transaction_approve.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
// Ensure session started, user is admin, CSRF protection is in place etc.
session_start(); // Ensure session is started to read the cookie
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

// --- Input Validation ---
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
$db = (new Database())->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error approving transaction: Database connection failed.");
    exit;
}

$db->beginTransaction();

try {
    // 1. Verify Transaction Exists and is Pending or Rejected (Allow re-approval)
    $stmt_check = $db->prepare("
        SELECT status, user_id, num_account, start_time, end_time
        FROM registration
        WHERE id = :id AND deleted_at IS NULL
        FOR UPDATE
    ");
    $stmt_check->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    // Allow approving if pending or previously rejected
    if (!in_array($registration['status'], ['pending', 'rejected'])) {
        throw new Exception("Transaction cannot be approved (Current Status: " . $registration['status'] . "). Only pending or rejected transactions can be approved.");
    }
    $old_status = $registration['status']; // Store old status for logging



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
    if (!$updated_reg) {
        throw new Exception("Failed to update registration status and times.");
    }

    // 3. Update Payment Confirmation (if applicable)
    $stmt_update_pay = $db->prepare("UPDATE payment SET confirmed = 1, confirmed_at = NOW(), updated_at = NOW() WHERE registration_id = :id");
    $stmt_update_pay->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_update_pay->execute();

    // 4. Activate Associated Survey Account(s)
    $stmt_activate_acc = $db->prepare("UPDATE survey_account SET enabled = 1, updated_at = NOW() WHERE registration_id = :id AND deleted_at IS NULL");
    $stmt_activate_acc->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $activated_acc = $stmt_activate_acc->execute();
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
    $host     = $_SERVER['HTTP_HOST']; 
    $url      = $protocol . '://' . $host . '/private/actions/account/create_account.php';

    $username   = 'user' . $transaction_id . '_' . time();
    $password   = bin2hex(random_bytes(8));
    $payload = [
        'registration_id' => $transaction_id,
        'username_acc'    => $username,
        'password_acc'    => $password,
        'start_time'      => $new_start_time_sql, // Use new start time
        'end_time'        => $new_end_time_sql,   // Use new end time
        'enabled'         => 1,
        'concurrent_user' => 1
    ];

    // --- Release session lock before internal request ---
    session_write_close(); 
    // --- End Release session lock ---

    // call internal script
    $accountCreationFailedDueToTimeout = false; // Flag for timeout
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);  // fail fast on connect
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
    if (isset($_COOKIE[session_name()])) {
        curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
    }

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch); // Get the cURL error number
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME); // Get total time taken
    curl_close($ch);

    // --- Re-open session if needed later in the script ---
    // session_start(); // Uncomment if you need to write to $_SESSION after this point
    // --- End Re-open session ---

    // Check specifically for timeout error
    if ($curlErrno === CURLE_OPERATION_TIMEDOUT) {
         error_log("Warning: Account creation request timed out for registration ID $transaction_id after {$totalTime} seconds. Approval committed, but account creation needs verification.");
         $accountCreationFailedDueToTimeout = true; // Set flag, but don't throw exception
         // Reset username/password as they are unknown due to timeout
         $username = null;
         $password = null;
    } elseif (!$accountCreationFailedDueToTimeout) { // Only proceed with other checks if no timeout occurred
        if ($curlError) {
            throw new Exception("Account creation request failed (cURL Error): " . $curlError);
        }
        // Allow HTTP status 0 (often indicates success in internal/CLI contexts) or 200
        if ($httpCode !== 200 && $httpCode !== 0) {
            // Include more of the response body for debugging non-JSON errors (like HTML error pages)
            throw new Exception("Account creation failed (HTTP Status $httpCode): " . substr(strip_tags($result ?: ''), 0, 500));
        }

        $resData = json_decode($result, true);
        if (!is_array($resData)) {
            // Include the raw response if JSON decoding fails
            throw new Exception("Invalid JSON response from account creation service. Response: " . substr($result ?: '', 0, 500));
        }
        if (empty($resData['success'])) {
            throw new Exception("Account creation failed: " . ($resData['message'] ?? 'Unknown error from account service'));
        }
        // use returned credentials in response
        $username = $resData['account']['username'];
        $password = $resData['account']['password'];
    }

    // 6. Update Transaction History
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'completed');

    // 7. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'approve_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'active']); // Example

    // 8. TODO: Send Notifications (Email/SMS to user?)

    // --- Commit Transaction ---
    $db->commit();

    // --- Prepare final response ---
    $responseMessage = 'Transaction #' . $transaction_id . ' approved.';
    $accountDetails = null;

    if ($accountCreationFailedDueToTimeout) {
        $responseMessage .= ' However, the account creation request timed out. Please verify account status manually.';
        $accountDetails = ['username' => 'Pending (Timeout)', 'password' => 'Pending (Timeout)'];
    } elseif ($username && $password) {
        $responseMessage .= ' Account created successfully.';
        $accountDetails = ['username' => $username, 'password' => $password];
    } else {
         // This case might occur if the create_account script succeeded but didn't return expected data (should have been caught earlier)
         error_log("Warning: Transaction approved but account details missing for registration ID $transaction_id without explicit timeout.");
         $responseMessage .= ' Account details might be missing or pending.';
         $accountDetails = ['username' => 'Unknown', 'password' => 'Unknown'];
    }

    echo json_encode([
        'success' => true,
        'message' => $responseMessage,
        'account' => $accountDetails
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