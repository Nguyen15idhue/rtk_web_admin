<?php
// filepath: private\actions\purchase\process_transaction_revert.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

// --- Permission check ---
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin']); 

require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/services/TransactionHistoryService.php'; 
require_once BASE_PATH . '/classes/TransactionModel.php';
require_once BASE_PATH . '/classes/AccountModel.php'; 
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
$database = Database::getInstance();
$db       = $database->getConnection();

if (!$db) {
    api_error('Database connection failed.', 500);
    error_log("Error reverting transaction: Database connection failed.");
    $database->close();
    exit;
}

try {
    $db->beginTransaction();
    $tm = new TransactionModel();

    $th = $tm->getHistoryById($transaction_id);
    if (!$th) throw new Exception("History not found");
    $reg_id = (int)$th['registration_id'];
    $tx_type = $th['transaction_type'];

    $reg = $tm->getRegistrationById($reg_id);
    if (!$reg || $reg['status'] !== 'active') {
        throw new Exception("Cannot revert: Registration not found or not active.");
    }

    // nếu renewal
    if ($tx_type === 'renewal') {
        // chỉ trừ lại thời gian đã cộng trên survey_account
        $tm->adjustAccountTimesForRevert($reg_id, $transaction_id);

        // cập nhật ngày start/end đã điều chỉnh lên RTK
        $accountModel = new AccountModel($db);
        $accounts     = $tm->getAllSurveyAccountsForRegistration($reg_id);
        $apiFailures  = [];
        foreach ($accounts as $account) {
            $payload   = $accountModel->buildRtkUpdatePayload($account['id'], []);
            error_log("[Revert Transaction {$transaction_id}] Updating RTK account dates for account {$account['id']}: " . print_r($payload, true));
            $apiResult = updateRtkAccount($payload);
            if (empty($apiResult['success'])) {
                $apiFailures[] = $account['id'];
                error_log("[Revert Transaction {$transaction_id}] FAILED to update RTK account {$account['id']}: " . $apiResult['error']);
            } else {
                error_log("[Revert Transaction {$transaction_id}] RTK update success for account {$account['id']}");
            }
        }
        if (!empty($apiFailures)) {
            throw new Exception('RTK update failed for accounts: ' . implode(',', $apiFailures));
        }
    }

    // chung: chuyển registration về pending và bỏ xác nhận thanh toán
    $tm->updateRegistrationStatus($reg_id, 'pending');
    $tm->unconfirmPayment($transaction_id);

    // chỉ xóa mềm và gọi API external khi không phải renewal
    if ($tx_type !== 'renewal') {
        $accounts = $tm->getAllSurveyAccountsForRegistration($reg_id);
        $tm->softDeleteAccounts($reg_id);
        $tm->updateHistoryStatus($transaction_id, 'pending');

        // track API failures & delay commit until after external calls
        $apiFailures = [];
        foreach ($accounts as $account) {
            error_log("[Revert Transaction {$transaction_id}] Deleting RTK account {$account['id']}");
            $apiResult = deleteRtkAccount([$account['id']]);
            if (empty($apiResult['success'])) {
                $apiFailures[] = $account['id'];
                error_log("[Revert Transaction {$transaction_id}] FAILED to delete RTK account {$account['id']}.");
            } else {
                error_log("[Revert Transaction {$transaction_id}] RTK delete success for account {$account['id']}.");
            }
        }
        if (!empty($apiFailures)) {
            throw new Exception('RTK deletion failed for account IDs: ' . implode(',', $apiFailures));
        }
    } else {
        // với renewal chỉ update history status
        $tm->updateHistoryStatus($transaction_id, 'pending');
    }
    
    // All good – now commit
    $db->commit();
    api_success(null, 'Transaction #' . $transaction_id . ' reverted successfully. Accounts handled.');

} catch (Exception $e) {
    $db->rollBack();
    error_log("Error reverting transaction ID $transaction_id: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    api_error('Failed to revert transaction. Please try again later or contact support.', 500);
} finally {
    $database->close();
}
?>
