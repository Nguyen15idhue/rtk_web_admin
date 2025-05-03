<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\purchase\process_transaction_reject.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
// Ensure session started, user is admin, CSRF protection is in place etc.
// session_start();
// if (!isset($_SESSION['admin_id']) || !check_admin_permission('transaction_reject')) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Permission denied.']);
//     exit;
// }
// if (!verify_csrf_token($_POST['csrf_token'] ?? '')) { // Example CSRF check
//     http_response_code(400);
//     echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
//     exit;
// }

require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/services/TransactionHistoryService.php'; 

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
$reason = trim(htmlspecialchars($input['reason'] ?? '')); // Sanitize reason

if ($transaction_id === false || $transaction_id <= 0) {
     http_response_code(400); // Bad Request
     echo json_encode(['success' => false, 'message' => 'Invalid or missing transaction ID.']);
     exit;
}
if (empty($reason)) {
     http_response_code(400); // Bad Request
     echo json_encode(['success' => false, 'message' => 'Rejection reason cannot be empty.']);
     exit;
}
// Optional: Add length limit for reason
// if (mb_strlen($reason) > 500) { ... }

// --- Processing ---
// Instantiate Database and get connection
$db = (Database::getInstance())->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error rejecting transaction: Database connection failed.");
    exit;
}

$db->beginTransaction();

try {
    // 1. Verify Transaction Exists and is Pending or Active (Allow rejecting approved)
    $stmt_check = $db->prepare("
        SELECT r.status, r.user_id, r.location_id, r.start_time, r.end_time
        FROM registration r
        WHERE r.id = :id AND r.deleted_at IS NULL FOR UPDATE
    "); // Lock row, fetch location_id, start/end times
    $stmt_check->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    // Allow rejecting if pending or active (approved)
    if (!in_array($registration['status'], ['pending', 'active'])) {
         throw new Exception("Transaction cannot be rejected (Current Status: " . $registration['status'] . "). Only pending or active transactions can be rejected.");
    }
    $old_status = $registration['status']; // Store old status for logging

    // --- Fetch associated survey accounts BEFORE updates (needed if rejecting an 'active' transaction) ---
    $accounts = [];
    if ($old_status === 'active') {
        $stmt_accounts = $db->prepare("
            SELECT id, username_acc, password_acc, concurrent_user, customerBizType
            FROM survey_account
            WHERE registration_id = :reg_id AND deleted_at IS NULL
        ");
        $stmt_accounts->bindParam(':reg_id', $transaction_id, PDO::PARAM_INT);
        $stmt_accounts->execute();
        $accounts = $stmt_accounts->fetchAll(PDO::FETCH_ASSOC);
    }
    // --- End Fetch ---

    // 2. Update Registration Status and Add Rejection Reason
    $sql_update = "UPDATE registration SET status = 'rejected', rejection_reason = :reason, updated_at = NOW() WHERE id = :id";
    $stmt_update_reg = $db->prepare($sql_update);
    $stmt_update_reg->bindParam(':reason', $reason, PDO::PARAM_STR); // Bind the reason
    $stmt_update_reg->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $updated = $stmt_update_reg->execute();

    if (!$updated) {
        throw new Exception("Failed to update registration status.");
    }

    // 3. If it was previously active, handle deactivation steps (like revert)
    if ($old_status === 'active') {
        // Deactivate Survey Account(s) in DB
        $stmt_deactivate_acc = $db->prepare("UPDATE survey_account SET enabled = 0, updated_at = NOW() WHERE registration_id = :id AND deleted_at IS NULL");
        $stmt_deactivate_acc->bindParam(':id', $transaction_id, PDO::PARAM_INT);
        $stmt_deactivate_acc->execute(); // Proceed even if no accounts found

        // Unconfirm Payment (if applicable)
        $stmt_unconfirm_pay = $db->prepare("UPDATE payment SET confirmed = 0, confirmed_at = NULL, updated_at = NOW() WHERE registration_id = :id");
        $stmt_unconfirm_pay->bindParam(':id', $transaction_id, PDO::PARAM_INT);
        $stmt_unconfirm_pay->execute();

        // --- New: Call RTK API to disable accounts ---
        if (!empty($accounts)) {
            $locationId = $registration['location_id'];
            $mountIds = getMountPointsByLocationId($locationId); // Get mount points

            foreach ($accounts as $account) {
                $apiPayload = [
                    'id'              => $account['id'],
                    'name'            => $account['username_acc'],
                    'userPwd'         => $account['password_acc'], // Assuming password doesn't change
                    'startTime'       => strtotime($registration['start_time']) * 1000, // Use original times
                    'endTime'         => strtotime($registration['end_time']) * 1000,
                    'enabled'         => 0, // <<< Set to disabled
                    'numOnline'       => $account['concurrent_user'] ?? 1,
                    'customerBizType' => $account['customerBizType'] ?? 1,
                    'mountIds'        => $mountIds,
                    // Other fields like userId, customerName etc., might be needed if API requires them
                ];
                error_log("[Reject Transaction {$transaction_id}] Calling RTK API for account {$account['id']} with payload: " . json_encode($apiPayload));
                $apiResult = updateRtkAccount($apiPayload);
                error_log("[Reject Transaction {$transaction_id}] RTK API response for account {$account['id']}: " . json_encode($apiResult));

                if (!$apiResult['success']) {
                    // Throw exception to rollback DB changes if API fails
                    throw new Exception("Failed to disable account {$account['id']} via RTK API during rejection: " . ($apiResult['error'] ?? 'Unknown API error'));
                }
            }
        } else {
             error_log("[Reject Transaction {$transaction_id}] No associated survey accounts found to disable via API (was active).");
        }
        // --- End RTK API Call ---
    }

    // 4. Update Transaction History
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'failed');

    // 5. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'reject_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'rejected', 'reason' => $reason]); // Example

    // 6. TODO: Send Notifications (Email/SMS to user about rejection?)

    // --- Commit Transaction ---
    $db->commit();
    $message = 'Transaction #' . $transaction_id . ' rejected successfully.';
    if ($old_status === 'active') {
        $message .= ' Associated accounts disabled.';
    }
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error rejecting transaction ID $transaction_id: " . $e->getMessage()); // Log detailed error
    http_response_code(500); // Internal Server Error
    // Provide a generic error message to the client
    echo json_encode(['success' => false, 'message' => 'Failed to reject transaction. Please try again later or contact support.']);
}

exit;
?>