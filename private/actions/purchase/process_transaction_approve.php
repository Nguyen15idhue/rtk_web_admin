<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\purchase\process_transaction_approve.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
// Ensure session started, user is admin, CSRF protection is in place etc.
// session_start();
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
    $stmt_check = $db->prepare("SELECT status, user_id FROM registration WHERE id = :id AND deleted_at IS NULL FOR UPDATE"); // Lock row
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

    // 2. Update Registration Status and clear rejection reason
    $stmt_update_reg = $db->prepare("UPDATE registration SET status = 'active', rejection_reason = NULL, updated_at = NOW() WHERE id = :id");
    $stmt_update_reg->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $updated_reg = $stmt_update_reg->execute();
    if (!$updated_reg) {
        throw new Exception("Failed to update registration status.");
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

    // 5. Update Transaction History
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'completed');

    // 6. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'approve_transaction', 'registration', $transaction_id, ['old_status' => $old_status], ['new_status' => 'active']); // Example

    // 7. TODO: Send Notifications (Email/SMS to user?)

    // --- Commit Transaction ---
    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Transaction #' . $transaction_id . ' approved successfully.']);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error approving transaction ID $transaction_id: " . $e->getMessage()); // Log detailed error
    http_response_code(500); // Internal Server Error
    // Provide a generic error message to the client
    echo json_encode(['success' => false, 'message' => 'Failed to approve transaction:' . $e->getMessage()]);
}

exit;
?>