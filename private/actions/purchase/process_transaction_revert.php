<?php
// filepath: private\actions\purchase\process_transaction_revert.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Prerequisites ---
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

if ($transaction_id === false || $transaction_id <= 0) {
     http_response_code(400); // Bad Request
     echo json_encode(['success' => false, 'message' => 'Invalid or missing transaction ID.']);
     exit;
}

// --- Processing ---
$database = Database::getInstance();
$db       = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    error_log("Error reverting transaction: Database connection failed.");
    $database->close();
    exit;
}

try {
    $db->beginTransaction();

    // NEW: Map history ID → registration ID & transaction type
    $stmt_hist = $db->prepare("
        SELECT registration_id, transaction_type
        FROM transaction_history
        WHERE id = :hid FOR UPDATE
    ");
    $stmt_hist->bindParam(':hid', $transaction_id, PDO::PARAM_INT);
    $stmt_hist->execute();
    $hist = $stmt_hist->fetch(PDO::FETCH_ASSOC);
    if (!$hist) {
        throw new Exception("Transaction history not found (ID: {$transaction_id}).");
    }
    $reg_id  = (int)$hist['registration_id'];
    $tx_type = $hist['transaction_type'];

    // 1. Verify Transaction Exists and is Active
    $stmt_check = $db->prepare("
        SELECT status, user_id, location_id, start_time, end_time
        FROM registration
        WHERE id = :id AND deleted_at IS NULL FOR UPDATE
    ");
    $stmt_check->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $registration = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception("Transaction not found or deleted.");
    }
    if ($registration['status'] !== 'active') {
         throw new Exception("Transaction is not approved (Current Status: " . $registration['status'] . "). Only approved transactions can be reverted.");
    }

    // --- NEW: nếu lịch sử là 'renewal' thì trừ lại end_time ---
    if ($tx_type === 'renewal') {
        $stmt_adj = $db->prepare("
            UPDATE registration
            SET end_time = start_time, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt_adj->bindParam(':id', $reg_id, PDO::PARAM_INT);
        $stmt_adj->execute();
    }

    // --- Fetch associated survey accounts BEFORE updates ---
    $stmt_accounts = $db->prepare("
        SELECT id, username_acc, password_acc, concurrent_user, customerBizType
        FROM survey_account
        WHERE registration_id = :id AND deleted_at IS NULL
    ");
    $stmt_accounts->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $stmt_accounts->execute();
    $accounts = $stmt_accounts->fetchAll(PDO::FETCH_ASSOC);
    // --- End Fetch ---

    // 2. Update Registration Status back to Pending
    $stmt_update_reg = $db->prepare("
        UPDATE registration
        SET status = 'pending', updated_at = NOW()
        WHERE id = :id
    ");
    $stmt_update_reg->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $stmt_update_reg->execute();

    // 3. Update Payment Confirmation back to Unconfirmed
    $stmt_unconfirm_pay = $db->prepare("
        UPDATE transaction_history
        SET payment_confirmed = 0, payment_confirmed_at = NULL, updated_at = NOW()
        WHERE id = :hid
    ");
    $stmt_unconfirm_pay->bindParam(':hid', $transaction_id, PDO::PARAM_INT);
    $stmt_unconfirm_pay->execute();

    // 4. Delete Associated Survey Account(s) in DB (soft delete)
    $stmt_delete_acc = $db->prepare("
        UPDATE survey_account 
        SET deleted_at = NOW(), updated_at = NOW() 
        WHERE registration_id = :id AND deleted_at IS NULL
    ");
    $stmt_delete_acc->bindParam(':id', $reg_id, PDO::PARAM_INT);
    $stmt_delete_acc->execute();

    // --- New: Call RTK API to delete accounts ---
    if (!empty($accounts)) {
        foreach ($accounts as $account) {
            error_log("[Revert Transaction {$transaction_id}] Deleting RTK account {$account['id']}");
            $apiResult = deleteRtkAccount([$account['id']]);
            error_log("[Revert Transaction {$transaction_id}] RTK delete response for account {$account['id']}: " . json_encode($apiResult));
            if (!$apiResult['success']) {
                throw new Exception("Failed to delete account {$account['id']} via RTK API: " . ($apiResult['error'] ?? 'Unknown API error'));
            }
        }
    } else {
        error_log("[Revert Transaction {$transaction_id}] No associated survey accounts found to delete via API.");
    }

    // 5. Update Transaction History back to Pending
    TransactionHistoryService::updateStatusByRegistrationId($db, $transaction_id, 'pending');

    // 6. Log Activity
    // log_admin_activity($_SESSION['admin_id'], 'revert_transaction', 'registration', $transaction_id, ['old_status' => 'active'], ['new_status' => 'pending']); // Example

    // --- Commit Transaction ---
    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Transaction #' . $transaction_id . ' reverted to pending successfully. Associated accounts deleted.']);

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error reverting transaction ID $transaction_id: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());          // <-- Added detailed stack trace
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Failed to revert transaction. Please try again later or contact support.']);
} finally {
    $database->close();
}

exit;
?>
