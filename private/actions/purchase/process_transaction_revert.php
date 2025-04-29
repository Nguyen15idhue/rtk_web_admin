<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\purchase\process_transaction_revert.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
// session_start();
// if (!isset($_SESSION['admin_id']) || !check_admin_permission('transaction_revert')) { // Add permission check if needed
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
require_once __DIR__ . '/../../services/TransactionHistoryService.php'; // Service to manage transaction history
// require_once __DIR__ . '/../../utils/logger.php'; // Example: For logging actions

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
$db = (Database::getInstance())->getConnection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error reverting transaction: Database connection failed.");
    exit;
}

$db->beginTransaction();

try {
    // 1. Verify Transaction Exists and is Active (Approved)
    $stmt_check = $db->prepare("SELECT status, user_id FROM registration WHERE id = :id AND deleted_at IS NULL FOR UPDATE"); // Lock row
    $stmt_check->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    if ($registration['status'] !== 'active') {
         throw new Exception("Transaction is not approved (Current Status: " . $registration['status'] . "). Only approved transactions can be reverted.");
    }

    // 2. Update Registration Status back to Pending
    // Keep rejection_reason as NULL or clear it if desired
    $stmt_update_reg = $db->prepare("UPDATE registration SET status = 'pending', updated_at = NOW() WHERE id = :id");
    $stmt_update_reg->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $updated_reg = $stmt_update_reg->execute();
    if (!$updated_reg) {
        throw new Exception("Failed to update registration status.");
    }

    // 3. Update Payment Confirmation back to Unconfirmed
    $stmt_unconfirm_pay = $db->prepare("UPDATE payment SET confirmed = 0, confirmed_at = NULL, updated_at = NOW() WHERE registration_id = :id");
    $stmt_unconfirm_pay->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_unconfirm_pay->execute(); // Proceed even if no payment record exists

    // 4. Deactivate Associated Survey Account(s)
    $stmt_deactivate_acc = $db->prepare("UPDATE survey_account SET enabled = 0, updated_at = NOW() WHERE registration_id = :id AND deleted_at IS NULL");
    $stmt_deactivate_acc->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    $stmt_deactivate_acc->execute(); // Proceed even if no accounts found

    // 5. Update Transaction History back to Pending
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'pending');
    // Example direct update:
    // $stmt_update_th = $db->prepare("UPDATE transaction_history SET status = 'pending', updated_at = NOW() WHERE registration_id = :id AND status = 'completed'");
    // $stmt_update_th->bindParam(':id', $transaction_id, PDO::PARAM_INT);
    // $stmt_update_th->execute();

    // 6. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'revert_transaction', 'registration', $transaction_id, ['old_status' => 'active'], ['new_status' => 'pending']); // Example

    // --- Commit Transaction ---
    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Transaction #' . $transaction_id . ' reverted to pending successfully.']);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error reverting transaction ID $transaction_id: " . $e->getMessage()); // Log detailed error
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Failed to revert transaction. Please try again later or contact support.']);
}

exit;
?>
