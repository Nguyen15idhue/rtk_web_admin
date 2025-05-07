<?php
// filepath: private\actions\purchase\process_transaction_reject.php
declare(strict_types=1);
header('Content-Type: application/json'); // Set response type

require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin']); 

require_once __DIR__ . '/../../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/services/TransactionHistoryService.php'; 
require_once BASE_PATH . '/classes/TransactionModel.php';

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
    $th = $tm->getHistoryById($transaction_id);
    if (!$th) throw new Exception("Transaction history not found");
    $reg_id = (int)$th['registration_id'];

    // nếu là renewal
    if ($th['transaction_type'] === 'renewal') {
        $tm->resetEndTime($reg_id);
    }

    $reg = $tm->getRegistrationById($reg_id);
    if (!$reg) throw new Exception("Registration not found");
    $old_status = $reg['status'];

    $accounts = [];
    // Fetch all accounts that were ever associated with this registration,
    // regardless of their current local deleted_at status,
    // to ensure their RTK counterparts are handled during rejection.
    // We only need to do this if the registration was 'active', implying accounts might exist on RTK.
    if ($old_status === 'active') {
        $accounts = $tm->getAllSurveyAccountsForRegistration($reg_id);
    }

    $tm->updateRegistrationStatus($reg_id, 'rejected', $reason);

    if ($old_status === 'active') {
        $tm->softDeleteAccounts($reg_id);
        $tm->unconfirmPayment($transaction_id);
    }

    $tm->updateHistoryStatus($transaction_id, 'failed');

    // --- NEW: track API failures & delay commit until after external calls ---
    $apiFailures = [];
    if (!empty($accounts)) {
        foreach ($accounts as $account) {
            error_log("[Reject Transaction {$transaction_id}] Deleting RTK account {$account['id']}");
            $apiResult = deleteRtkAccount([$account['id']]);
            if (empty($apiResult['success'])) {
                $apiFailures[] = $account['id'];
                error_log("[Reject Transaction {$transaction_id}] FAILED to delete RTK account {$account['id']}.");
            } else {
                error_log("[Reject Transaction {$transaction_id}] RTK delete success for account {$account['id']}.");
            }
        }
    } else {
        error_log("[Reject Transaction {$transaction_id}] No associated survey accounts to delete via API.");
    }

    // If any external deletes failed, abort and rollback everything
    if (!empty($apiFailures)) {
        throw new Exception('RTK deletion failed for account IDs: ' . implode(',', $apiFailures));
    }

    // All good – now commit
    $db->commit();
    api_success(null, 'Transaction #' . $transaction_id . ' rejected successfully.');
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error rejecting transaction ID $transaction_id: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    api_error('Failed to reject transaction. Please try again later or contact support.', 500);
} finally {
    $database->close();
}
?>