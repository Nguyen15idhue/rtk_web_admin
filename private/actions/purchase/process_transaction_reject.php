<?php
// filepath: private\actions\purchase\process_transaction_reject.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('invoice_management_edit');

require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/services/TransactionHistoryService.php'; 
require_once BASE_PATH . '/classes/TransactionModel.php';
require_once BASE_PATH . '/classes/AccountModel.php'; 
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
$reason = trim(htmlspecialchars($input['reason'] ?? '')); // Sanitize reason

if ($transaction_id === false || $transaction_id <= 0) {
    api_error('Invalid or missing transaction ID.', 400);
}
if (empty($reason)) {
    api_error('Rejection reason cannot be empty.', 400);
}
// Optional: Add length limit for reason
// if (mb_strlen($reason) > 500) { ... }

// --- Processing ---
$database = Database::getInstance();
$db       = $database->getConnection();

if (!$db) {
    api_error('Database connection failed.', 500);
    error_log("Error rejecting transaction: Database connection failed.");
    $database->close();
    exit;
}

$db->beginTransaction();
$tm = new TransactionModel();

try {
    $th       = $tm->getHistoryById($transaction_id);
    if (!$th) throw new Exception("Transaction history not found");
    $reg_id   = (int)$th['registration_id'];
    $tx_type  = $th['transaction_type'];
    $reg      = $tm->getRegistrationById($reg_id);
    if (!$reg) throw new Exception("Registration not found");

    // --- If renewal: adjust in DB and defer API calls ---
    if ($tx_type === 'renewal') {
        $tm->adjustAccountTimesForRevert($reg_id, $transaction_id);
    }

    // --- Common: revert registration and payment confirmation ---
    $tm->updateRegistrationStatus($reg_id, 'rejected', $reason);
    $tm->unconfirmPayment($transaction_id);

    // --- Non-renewal: hard-delete in DB and defer RTK deletion ---
    if ($tx_type !== 'renewal') {
        $accountsToDelete = $tm->getAllSurveyAccountsForRegistration($reg_id);
        $tm->hardDeleteAccounts($reg_id);
        $tm->updateHistoryStatus($transaction_id, 'failed');
    } else {
        // renewal: only update history in DB
        $tm->updateHistoryStatus($transaction_id, 'failed');
    }

    // Commit all DB changes before external API calls
    $db->commit();

    // --- External RTK API calls ---
    if ($tx_type === 'renewal') {
        $accountsToUpdate = $tm->getAllSurveyAccountsForRegistration($reg_id);
        $apiFailures = [];
        foreach ($accountsToUpdate as $account) {
            $payload = (new AccountModel($db))->buildRtkUpdatePayload($account['id'], []);
            error_log("[Reject Transaction {$transaction_id}] Updating RTK account dates for account {$account['id']}: " . print_r($payload, true));
            $result = updateRtkAccount($payload);
            if (empty($result['success'])) {
                $apiFailures[] = $account['id'];
                error_log("[Reject Transaction {$transaction_id}] FAILED to update RTK account {$account['id']}: " . $result['error']);
            } else {
                error_log("[Reject Transaction {$transaction_id}] RTK update success for account {$account['id']}");
            }
        }
        if (!empty($apiFailures)) {
            error_log("RTK update failures for accounts: " . implode(',', $apiFailures));
        }
    } else {
        // Non-renewal: delete RTK accounts
        $apiFailures = [];
        foreach ($accountsToDelete as $account) {
            error_log("[Reject Transaction {$transaction_id}] Deleting RTK account {$account['id']}");
            $result = deleteRtkAccount([$account['id']]);
            if (empty($result['success'])) {
                $apiFailures[] = $account['id'];
                error_log("[Reject Transaction {$transaction_id}] FAILED to delete RTK account {$account['id']}");
            } else {
                error_log("[Reject Transaction {$transaction_id}] RTK delete success for account {$account['id']}");
            }
        }
        if (!empty($apiFailures)) {
            error_log("RTK deletion failures for accounts: " . implode(',', $apiFailures));
        }
    }

    // Log activity after all operations
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'      => $reg['user_id'],
            ':action'       => 'reject_transaction',
            ':entity_type'  => 'transaction',
            ':entity_id'    => $transaction_id,
            ':old_values'   => json_encode(['status' => $th['status']]),
            ':new_values'   => json_encode(['status' => 'failed', 'reason' => $reason, 'registration_id' => $reg_id]),
            ':notify_content'  => "Giao dịch #{$transaction_id} đã bị từ chối. Lý do: {$reason}"
        ]
    );

    api_success(null, 'Từ chối thành công giao dịch #' . $transaction_id . '.');
    exit;
} catch (Exception $e) {
    // If an exception occurred before commit, rollback
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Error rejecting transaction ID $transaction_id: " . $e->getMessage());
    api_error('Failed to reject transaction. Please try again later or contact support.', 500);
} finally {
    $database->close();
}
?>