<?php
// filepath: private\actions\account\manual_renew_account.php
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db        = $bootstrap['db'];

Auth::ensureAuthorized('account_management');

require_once BASE_PATH . '/classes/AccountModel.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php'; // For updateRtkAccount
require_once BASE_PATH . '/utils/functions.php'; // For getMountPointsByLocationId if needed by buildRtkUpdatePayload

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
}

$input = $_POST; // Form data

$accountId       = $input['id'] ?? null;
$newPackageId    = !empty($input['package_id']) ? (int)$input['package_id'] : null;
$newActivationDate = $input['activation_date'] ?? null;
$newExpiryDate     = $input['expiry_date'] ?? null;

if (!$accountId || !$newActivationDate || !$newExpiryDate) {
    api_error('Thiếu thông tin cần thiết (ID tài khoản, ngày kích hoạt, ngày hết hạn).', 400);
}

try {
    $accountModel = new AccountModel($db);
    $currentAccount = $accountModel->getAccountById($accountId);

    if (!$currentAccount) {
        api_error('Không tìm thấy tài khoản.', 404);
    }

    $db->beginTransaction();

    // 1. Update survey_account
    $surveyAccountData = [
        'start_time' => $newActivationDate . ' 00:00:00',
        'end_time'   => $newExpiryDate . ' 23:59:59',
        'enabled'    => 1, // Ensure account is enabled upon renewal
    ];
    // Password is not changed during renewal, so it's not included here.
    // AccountModel->updateAccount handles not changing password if not provided.
    
    $updateSurveySuccess = $accountModel->updateAccount($accountId, $surveyAccountData);
    if (!$updateSurveySuccess) {
        throw new Exception('Không thể cập nhật thông tin survey_account.');
    }

    // 2. Update registration table
    $regId = $currentAccount['registration_id'];
    $updateRegFields = [
        'status = "active"',
        'start_time = :start_time',
        'end_time = :end_time',
        'updated_at = NOW()'
    ];
    $regParams = [
        ':start_time' => $newActivationDate . ' 00:00:00',
        ':end_time'   => $newExpiryDate . ' 23:59:59',
        ':reg_id'     => $regId
    ];

    if ($newPackageId && $newPackageId !== (int)$currentAccount['package_id']) {
        $updateRegFields[] = 'package_id = :package_id';
        $regParams[':package_id'] = $newPackageId;
    }

    $stmtReg = $db->prepare("UPDATE registration SET " . implode(', ', $updateRegFields) . " WHERE id = :reg_id");
    $updateRegSuccess = $stmtReg->execute($regParams);

    if (!$updateRegSuccess) {
        throw new Exception('Không thể cập nhật thông tin registration.');
    }
    
    // 3. Prepare data and call RTK API
    // The buildRtkUpdatePayload method needs all relevant current and new data.
    // We pass the $input from the form, which layouts new dates.
    // It will merge with existing data from DB where necessary.
    $rtkPayloadInput = [
        'id'              => $accountId, // Critical for AccountModel to fetch full existing data
        'activation_date' => $newActivationDate,
        'expiry_date'     => $newExpiryDate,
        'package_id'      => $newPackageId ?: $currentAccount['package_id'], // Use new or fallback to current
        'enabled'         => 1, // Ensure RTK account is enabled
        // Other fields will be pulled from DB by buildRtkUpdatePayload or kept as is
    ];
    
    $rtkApiPayload = $accountModel->buildRtkUpdatePayload($accountId, $rtkPayloadInput);
    error_log("[manual_renew_account] RTK API Payload: " . print_r($rtkApiPayload, true));
    
    $apiResult = updateRtkAccount($rtkApiPayload);
    error_log("[manual_renew_account] RTK API Result: " . print_r($apiResult, true));

    if (!($apiResult['success'] ?? false)) {
        // Log the error but don't necessarily roll back DB changes,
        // as admin might need to fix RTK manually.
        // Or, decide on a stricter policy (e.g., throw Exception to rollback).
        error_log("RTK API update failed for account {$accountId} during manual renewal. Message: " . ($apiResult['error'] ?? 'Unknown RTK error'));
        // For now, we'll proceed but the message will indicate potential RTK issue.
    }

    $db->commit();

    $refreshedAccount = $accountModel->getAccountById($accountId);
    $message = 'Gia hạn tài khoản thành công.';
    if (!($apiResult['success'] ?? false)) {
        $message .= ' Tuy nhiên, có lỗi khi cập nhật với hệ thống RTK: ' . ($apiResult['error'] ?? 'Unknown RTK error');
    }
    
    api_success(['account' => $refreshedAccount], $message);

} catch (PDOException $e) {
    $db->rollBack();
    error_log("Database error during manual renewal: " . $e->getMessage());
    api_error('Lỗi cơ sở dữ liệu khi gia hạn tài khoản.', 500);
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error during manual renewal: " . $e->getMessage());
    api_error($e->getMessage(), 500);
}
?>
