<?php
// filepath: private\actions\purchase\process_transaction_reject.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
// Ensure session started, user is admin, CSRF protection is in place etc.
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
$database = Database::getInstance();
$db       = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error rejecting transaction: Database connection failed.");
    $database->close();
    exit;
}

$db->beginTransaction();

try {
    // Map transaction_history ID to registration ID
    $stmt_th = $db->prepare("
        SELECT registration_id
        FROM transaction_history
        WHERE id = :th_id
        FOR UPDATE
    ");
    $stmt_th->bindParam(':th_id', $transaction_id, PDO::PARAM_INT);
    $stmt_th->execute();
    $thRecord = $stmt_th->fetch(PDO::FETCH_ASSOC);
    if (!$thRecord) {
        throw new Exception("Transaction history not found (ID: $transaction_id).");
    }
    $reg_id = (int)$thRecord['registration_id'];

    // --- NEW: nếu giao dịch này là renewal thì trừ lại end_time ---
    $stmt_type = $db->prepare("
        SELECT transaction_type 
        FROM transaction_history 
        WHERE id = :th_id
    ");
    $stmt_type->bindParam(':th_id', $transaction_id, PDO::PARAM_INT);
    $stmt_type->execute();
    if ($stmt_type->fetchColumn() === 'renewal') {
        $stmt_adj = $db->prepare("
            UPDATE registration 
            SET end_time = start_time, updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt_adj->bindParam(':id', $reg_id, PDO::PARAM_INT);
        $stmt_adj->execute();
    }

    // 1. Verify Registration Exists and is Pending or Active
    $stmt_check = $db->prepare("
        SELECT r.status, r.user_id, r.location_id, r.start_time, r.end_time
        FROM registration r
        WHERE r.id = :id AND r.deleted_at IS NULL
        FOR UPDATE
    ");
    $stmt_check->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception("Registration not found or deleted (ID: $reg_id).");
    }
    if (!in_array($registration['status'], ['pending', 'active'])) {
        throw new Exception("Transaction cannot be rejected (Current Status: " . $registration['status'] . ").");
    }
    $old_status = $registration['status'];

    // --- Fetch associated survey accounts BEFORE updates (needed if rejecting an 'active' transaction) ---
    $accounts = [];
    if ($old_status === 'active') {
        $stmt_accounts = $db->prepare("
            SELECT id, username_acc, password_acc, concurrent_user, customerBizType
            FROM survey_account
            WHERE registration_id = :reg_id AND deleted_at IS NULL
        ");
        $stmt_accounts->bindParam(':reg_id', $reg_id, PDO::PARAM_INT);
        $stmt_accounts->execute();
        $accounts = $stmt_accounts->fetchAll(PDO::FETCH_ASSOC);
    }
    // --- End Fetch ---

    // 2. Update Registration Status and Add Rejection Reason
    $sql_update = "UPDATE registration SET status = 'rejected', rejection_reason = :reason, updated_at = NOW() WHERE id = :id";
    $stmt_update_reg = $db->prepare($sql_update);
    $stmt_update_reg->bindParam(':reason', $reason, PDO::PARAM_STR); // Bind the reason
    $stmt_update_reg->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $updated = $stmt_update_reg->execute();

    if (!$updated) {
        throw new Exception("Failed to update registration status.");
    }

    // 3. If it was previously active, handle deactivation steps (like revert)
    if ($old_status === 'active') {
        // Soft delete Survey Account(s) in DB
        $stmt_delete_acc = $db->prepare("
            UPDATE survey_account
            SET deleted_at = NOW(), updated_at = NOW()
            WHERE registration_id = :id AND deleted_at IS NULL
        ");
        $stmt_delete_acc->bindParam(':id', $reg_id, PDO::PARAM_INT);
        $stmt_delete_acc->execute();

        // Unconfirm Payment (if applicable)
        $stmt_unconfirm_pay = $db->prepare("UPDATE payment SET confirmed = 0, confirmed_at = NULL, updated_at = NOW() WHERE registration_id = :id");
        $stmt_unconfirm_pay->bindParam(':id', $reg_id, PDO::PARAM_INT);
        $stmt_unconfirm_pay->execute();

        // --- New: Call RTK API to delete accounts ---
        if (!empty($accounts)) {
            foreach ($accounts as $account) {
                error_log("[Reject Transaction {$transaction_id}] Deleting RTK account {$account['id']}");
                $apiResult = deleteRtkAccount([$account['id']]);
                error_log("[Reject Transaction {$transaction_id}] RTK delete response for account {$account['id']}: " . json_encode($apiResult));
                if (!$apiResult['success']) {
                    throw new Exception("Failed to delete account {$account['id']} via RTK API during rejection: " . ($apiResult['error'] ?? 'Unknown API error'));
                }
            }
        } else {
            error_log("[Reject Transaction {$transaction_id}] No associated survey accounts found to delete via API (was active).");
        }
        // --- End RTK API Call ---
    }

    // 4. Update Transaction History (use 'failed' since 'rejected' isn't in transaction_history enum)
    TransactionHistoryService::updateStatusByRegistrationId($db, $reg_id, 'failed');

    // 5. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'reject_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'rejected', 'reason' => $reason]); // Example

    // 6. TODO: Send Notifications (Email/SMS to user about rejection?)

    // --- Commit Transaction ---
    $db->commit();
    $message = 'Transaction #' . $transaction_id . ' rejected successfully.';
    if ($old_status === 'active') {
        $message .= ' Associated accounts deleted.';
    }
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error rejecting transaction ID $transaction_id: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());          // <-- Added detailed stack trace
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to reject transaction. Please try again later or contact support.']);
} finally {
    $database->close();
}

exit;
?>