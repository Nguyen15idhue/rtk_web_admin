<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\toggle_status.php
header('Content-Type: application/json');
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');
// Check admin login
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php'; // Uses updated deriveAccountStatus
require_once __DIR__ . '/../../utils/functions.php'; // Uses updated get_account_action_buttons, get_account_status_badge
require_once __DIR__ . '/../../api/rtk_system/account_api.php';

// Get input data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!$input || !isset($input['id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$accountId = filter_var($input['id'], FILTER_SANITIZE_SPECIAL_CHARS);
$action = filter_var($input['action'], FILTER_SANITIZE_SPECIAL_CHARS);

// Action determines the desired state of 'enabled'
if (!in_array($action, ['suspend', 'reactivate'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
    exit;
}

$enable = ($action === 'reactivate'); // true for reactivate (set enabled=1), false for suspend (set enabled=0)

$database = Database::getInstance();
$db = $database->getConnection();

if (!$db) {
    error_log("Database connection failed in toggle_status.php");
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

$accountModel = new AccountModel($db);

// --- Database Transaction (Optional but recommended) ---
$db->beginTransaction();

try {
    // Update the account's enabled status
    $success = $accountModel->toggleAccountStatus($accountId, $enable);

    if ($success) {
        // Fetch updated account details to get the new derived status and regenerate buttons/badge
        // Ensure getAccountById fetches all fields needed by get_account_action_buttons and deriveAccountStatus
        $updatedAccount = $accountModel->getAccountById($accountId);

        if (!$updatedAccount) {
             // If the account disappeared after update (unlikely), rollback and error
             $db->rollBack();
             throw new Exception("Failed to fetch updated account details after status toggle.");
        }

        // The derived_status is now calculated correctly within getAccountById using the new logic
        $newDerivedStatus = $updatedAccount['derived_status'];

        // Regenerate action buttons HTML using the updated function
        $newButtonsHtml = get_account_action_buttons($updatedAccount);
        // Regenerate status badge HTML using the updated function
        $newStatusBadgeHtml = get_account_status_badge($newDerivedStatus);

        // Prepare and call external RTK API to sync status
        $stmtPwd = $db->prepare("SELECT password_acc FROM survey_account WHERE id = ?");
        $stmtPwd->execute([$accountId]);
        $currentPwd = $stmtPwd->fetchColumn();

        // --- Mới: Lấy mountIds để không bị reset sau suspend/reactivate ---
        $stmtLoc = $db->prepare("
            SELECT r.location_id 
            FROM registration r 
            JOIN survey_account sa ON sa.registration_id = r.id 
            WHERE sa.id = ?
        ");
        $stmtLoc->execute([$accountId]);
        $locationId = (int)$stmtLoc->fetchColumn();
        $mountIds = getMountPointsByLocationId($locationId); // Hàm có sẵn trong utils/functions.php

        // Prepare payload for RTK API to sync status, thêm mountIds
        $apiPayload = [
            'id'              => $accountId,
            'name'            => $updatedAccount['username_acc'],
            'userPwd'         => $currentPwd,
            'startTime'       => strtotime($updatedAccount['activation_date']) * 1000,
            'endTime'         => strtotime($updatedAccount['expiry_date'])   * 1000,
            'enabled'         => $enable ? 1 : 0,
            'numOnline'       => $updatedAccount['concurrent_user']   ?? 1,
            'customerBizType' => $updatedAccount['customerBizType']   ?? 1,
            'mountIds'        => $mountIds
        ];
        // Log payload for debugging
        error_log("RTK API Payload: " . json_encode($apiPayload));
        $apiResult = updateRtkAccount($apiPayload);
        // Log response from RTK API
        error_log("RTK API Response: " . json_encode($apiResult));
        if (!$apiResult['success']) {
            // External API failed: commit DB but notify front‑end
            $db->commit();
            echo json_encode([
                'success'            => false,
                'message'            => 'Account status updated but external API error: ' . $apiResult['error'],
                'newStatus'          => $newDerivedStatus,
                'newStatusBadgeHtml' => $newStatusBadgeHtml,
                'newButtonsHtml'     => $newButtonsHtml
            ]);
            exit;
        }
        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Account status updated successfully.',
            'newStatus' => $newDerivedStatus,
            'newStatusBadgeHtml' => $newStatusBadgeHtml,
            'newButtonsHtml' => $newButtonsHtml
        ]);
    } else {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to update account status in database.']);
    }
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error in toggle_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
