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

        // Replace old payment table update with transaction_history unconfirm
        $stmt_unconfirm_pay = $db->prepare("
            UPDATE transaction_history
            SET payment_confirmed = 0,
                payment_confirmed_at = NULL,
                updated_at = NOW()
            WHERE id = :th_id
        ");
        $stmt_unconfirm_pay->bindParam(':th_id', $transaction_id, PDO::PARAM_INT);
        $stmt_unconfirm_pay->execute();
    }

    // 4. Update Transaction History (use 'failed' since 'rejected' isn't in transaction_history enum)
    TransactionHistoryService::updateStatusByRegistrationId($db, $reg_id, 'failed');

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