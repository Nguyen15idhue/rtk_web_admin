<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\toggle_status.php
session_start();
header('Content-Type: application/json');

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php'; // Uses updated deriveAccountStatus
require_once __DIR__ . '/../../utils/functions.php'; // Uses updated get_account_action_buttons, get_account_status_badge

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

$database = new Database();
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

        // Log activity (implement logging function if needed)
        // log_activity($_SESSION['admin_id'], $action, 'survey_account', $accountId, ['enabled' => !$enable], ['enabled' => $enable]);

        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Account status updated successfully.',
            'newStatus' => $newDerivedStatus, // Send back the calculated derived status string
            'newStatusBadgeHtml' => $newStatusBadgeHtml, // Send back the new badge HTML
            'newButtonsHtml' => $newButtonsHtml // Send back the new buttons HTML
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
