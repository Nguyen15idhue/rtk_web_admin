<?php
// filepath: private\actions\account\toggle_status.php
header('Content-Type: application/json');

// Khởi bootstrap
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('account_management'); 
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

// Get input data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!$input || !isset($input['id']) || !isset($input['action'])) {
    abort('Invalid input.', 400);
    exit;
}

$accountId = filter_var($input['id'], FILTER_SANITIZE_SPECIAL_CHARS);
$action = filter_var($input['action'], FILTER_SANITIZE_SPECIAL_CHARS);

// Action determines the desired state of 'enabled'
if (!in_array($action, ['suspend', 'reactivate'])) {
    abort('Invalid action specified.', 400);
    exit;
}

$enable = ($action === 'reactivate'); // true for reactivate (set enabled=1), false for suspend (set enabled=0)

$accountModel = new AccountModel($db);

// --- Database Transaction (Optional but recommended) ---
$db->beginTransaction();

try {
    // Update the account's enabled status
    $success = $accountModel->toggleAccountStatus($accountId, $enable);

    if (!$success) {
        $db->rollBack();
        abort('Failed to update account status in database.', 500);
    }

    // Fetch updated account details to get the new derived status and regenerate buttons/badge
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
    $newStatusBadgeHtml = get_status_badge('account', $newDerivedStatus);

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
    $mountIds = getMountPointsByLocationId($locationId); 

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

    $payload = [
        'newStatus'          => $newDerivedStatus,
        'newStatusBadgeHtml' => $newStatusBadgeHtml,
        'newButtonsHtml'     => $newButtonsHtml
    ];

    if (!$apiResult['success']) {
        // External API failed: commit DB but notify front‑end
        $db->commit();
        api_error(
            'Account status updated but external API error: ' . $apiResult['error'],
            400,
            [],
            [] // errors array; payload remains in data
        );
    }

    $db->commit();
    api_success($payload, 'Account status updated successfully.');
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error in toggle_status.php: " 
        . $e->getMessage() 
        . "\nTrace: " . $e->getTraceAsString()
        . "\nPayload: " . json_encode($input ?? [])
    );
    abort('An error occurred: ' . $e->getMessage(), 500);
}
?>
