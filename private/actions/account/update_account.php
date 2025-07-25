<?php
// filepath: private\actions\account\update_account.php
// Khởi bootstrap để có $db, BASE_PATH, BASE_URL
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db        = $bootstrap['db'];
$base_path = $bootstrap['base_path'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Use Auth class for authentication and authorization
Auth::ensureAuthorized('account_management_edit');

require_once BASE_PATH . '/classes/AccountModel.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';
require_once BASE_PATH . '/classes/ActivityLogModel.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort('Invalid request method.', 405);
}

$rawInput = file_get_contents('php://input');
error_log("[update_account] raw input: " . $rawInput);
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}
error_log("[update_account] decoded input: " . print_r($input, true));
// debug: log the customer_phone value from input
error_log("[update_account] input customer_phone: " . ($input['customer_phone'] ?? '<none>'));
$accountId = $input['id'] ?? null;

// Basic validation
if (!$accountId || empty($input['username_acc'])) {
    abort('Missing required fields (Account ID, Username).', 400);
}

// --- XỬ LÝ TRẠNG THÁI ---
$inputStatus = $input['status'] ?? null;
$regStatus = in_array($inputStatus, ['pending','rejected']) 
             ? $inputStatus 
             : 'active';

try {
    if (!$db) {
        throw new Exception("Database connection failed.");
    }

    // Start transaction for data consistency
    $db->beginTransaction();
    
    try {
        $accountModel = new AccountModel($db);

        // Check if username already exists (excluding the current account)
        if ($accountModel->usernameExists($input['username_acc'], $accountId)) {
             abort('Username already exists.', 409);
        }

        // Prepare data for update (filter out ID)
        $updateData = $input;
        unset($updateData['id']); // Don't try to update the ID itself
        error_log("[update_account] updateData initial: " . print_r($updateData, true));

        // override enabled based on status select
        if ($inputStatus !== null) {
            $updateData['enabled'] = ($inputStatus === 'suspended') ? 0 : 1;
        }

        // Handle password: only include if not empty
        if (empty($updateData['password_acc'])) {
            unset($updateData['password_acc']);
        }

        // Ensure boolean/int types are correct
        if (isset($updateData['enabled'])) {
            $updateData['enabled'] = (int)$updateData['enabled'];
        }
        if (isset($updateData['concurrent_user'])) {
            $updateData['concurrent_user'] = (int)$updateData['concurrent_user'];
        }
         if (isset($updateData['user_type'])) {
            $updateData['user_type'] = empty($updateData['user_type']) ? null : (int)$updateData['user_type'];
        }
         if (isset($updateData['regionIds'])) {
            $updateData['regionIds'] = empty($updateData['regionIds']) ? null : (int)$updateData['regionIds'];
        }
         if (isset($updateData['customerBizType'])) {
            $updateData['customerBizType'] = empty($updateData['customerBizType']) ? null : (int)$updateData['customerBizType'];
        }

        // Update local database first
        $success = $accountModel->updateAccount($accountId, $updateData);

        if (!$success) {
            throw new Exception('Failed to update account in local database.');
        }

        error_log("[update_account] Successfully updated local database for account {$accountId}");

        // ---- Thay đổi ở đây: luôn dùng chung registration_id ----
        $stmtRegId = $db->prepare("SELECT registration_id FROM survey_account WHERE id = ?");
        $stmtRegId->execute([$accountId]);
        $regTargetId = (int)$stmtRegId->fetchColumn();

        // update only this registration row's status
        if ($inputStatus !== null) {
            $stmtReg = $db->prepare("
                UPDATE registration 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmtReg->execute([$regStatus, $regTargetId]);
        }

        // update only this registration row's start_time/end_time
        $activation = $input['activation_date'] ?? null;
        $expiry     = $input['expiry_date'] ?? null;
        if ($activation && $expiry) {

            // NEW: đồng bộ start_time/end_time trong survey_account
            $stmtAccTime = $db->prepare("
                UPDATE survey_account 
                SET start_time = ?, end_time = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmtAccTime->execute([
                $activation . ' 00:00:00',
                $expiry     . ' 23:59:59',
                $accountId
            ]);
        }

        // NEW: Update registration.package_id if provided in input
        if (isset($input['package_id']) && !empty($input['package_id'])) {
            // Fetch current package_id from registration to avoid unnecessary updates
            $stmtCurrentPkg = $db->prepare("SELECT package_id FROM registration WHERE id = ?");
            $stmtCurrentPkg->execute([$regTargetId]);
            $currentRegPackageId = (int)$stmtCurrentPkg->fetchColumn();

            if ((int)$input['package_id'] !== $currentRegPackageId) {
                $stmtPkg = $db->prepare("
                    UPDATE registration
                    SET package_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmtPkg->execute([(int)$input['package_id'], $regTargetId]);
                error_log("[update_account] Updated registration.package_id to {$input['package_id']} for registration {$regTargetId}");
            }
        }

        // update only this registration row's user_id
        $rtkUserId = null;
        $userEmail = filter_var($input['user_email'] ?? null, FILTER_VALIDATE_EMAIL);
        if ($userEmail) {
            $stmtUser = $db->prepare("SELECT id FROM `user` WHERE email = ?");
            $stmtUser->execute([$userEmail]);
            $newUserId = (int)$stmtUser->fetchColumn();
            if ($newUserId) {
                $stmtUpdUser = $db->prepare("
                    UPDATE registration
                    SET user_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmtUpdUser->execute([$newUserId, $regTargetId]);
                $rtkUserId = $newUserId; // store for payload
            }
        }

        // fetch existing password if no new password provided
        if (empty($input['password_acc'])) {
            $stmtPwd = $db->prepare("SELECT password_acc FROM survey_account WHERE id = ?");
            $stmtPwd->execute([$accountId]);
            $currentPwd = $stmtPwd->fetchColumn();
        } else {
            $currentPwd = $input['password_acc'];
        }

        // update only this registration row's location_id
        // lấy ID location cũ từ DB
        $stmtOldLoc = $db->prepare("
            SELECT r.location_id 
            FROM registration r 
            JOIN survey_account sa ON sa.registration_id = r.id 
            WHERE sa.id = ?
        ");
        $stmtOldLoc->execute([$accountId]);
        $oldLocationId = (int)$stmtOldLoc->fetchColumn();

        // NEW: xác thực location_id từ form
        $newLocationId = filter_var($input['location_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) ?: $oldLocationId;

        // nếu có thay đổi, cập nhật luôn trong registration để đồng bộ
        if ($newLocationId !== $oldLocationId) {
            $stmtUpdLoc = $db->prepare("
                UPDATE registration 
                SET location_id = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmtUpdLoc->execute([$newLocationId, $regTargetId]);
            error_log("Location changed for account {$accountId}: old={$oldLocationId}, new={$newLocationId}");
        }

        // tính mountIds dựa trên giá trị đã xác thực
        $mountIds = getMountPointsByLocationId($newLocationId);

        // NEW: prepare additional payload fields à la create_account
        $casterIds       = !empty($input['caster'])        ? [trim($input['caster'])] : [];
        $regionIdsArr    = isset($updateData['regionIds']) ? [$updateData['regionIds']] : [];
        $customerCompany = $input['customer_company'] ?? '';
        // get customer_phone: prefer input, else load from user table
        $customerPhone = $input['customer_phone'] ?? '';
        if (empty($customerPhone) && !empty($rtkUserId)) {
            $stmtPhone = $db->prepare("SELECT phone FROM `user` WHERE id = ?");
            $stmtPhone->execute([$rtkUserId]);
            $customerPhone = $stmtPhone->fetchColumn() ?: '';
        }
        // NEW: Sanitize phone number for RTK API requirements
        $customerPhone = preg_replace('/[^0-9+\-]/', '', $customerPhone); // Keep only digits, +, -
        $customerPhone = substr($customerPhone, 0, 20); // Limit to 20 chars
        error_log("[update_account] final sanitized customer_phone: " . $customerPhone);

        // get customer_name: prefer input, else load from user table
        $customerName = $input['customer_name'] ?? '';
        if (empty($customerName) && !empty($rtkUserId)) {
            $stmtName = $db->prepare("SELECT username FROM `user` WHERE id = ?");
            $stmtName->execute([$rtkUserId]);
            $customerName = $stmtName->fetchColumn() ?: '';
        }
        // FIXED: log customer_name, not phone
        error_log("[update_account] final customer_name: " . $customerName);

        // NEW: compute start/end in ms directly from submitted dates
        $startMs = strtotime($activation . ' 00:00:00') * 1000;
        $endMs   = strtotime($expiry     . ' 23:59:59') * 1000;

        // call external RTK update API with improved error handling
        $apiMsg = '';
        $apiSuccess = false;
        
        try {
            // Xây payload qua AccountModel
            $apiPayload = $accountModel->buildRtkUpdatePayload($accountId, $input);
            error_log("[update_account] RTK API payload via model: " . print_r($apiPayload, true));
            
            $apiResult = updateRtkAccount($apiPayload);
            error_log("[update_account] RTK API result: " . print_r($apiResult, true));

            // capture the informational message
            $apiMsg = $apiResult['data']['msg'] ?? '';
            $apiSuccess = $apiResult['success'];

            if (!$apiResult['success']) {
                // Log the API error but don't fail the whole operation since local DB is already updated
                error_log("[update_account] RTK API failed: " . ($apiResult['error'] ?? 'Unknown error'));
                $apiMsg = 'RTK API Error: ' . ($apiResult['error'] ?? 'Unknown error');
            }
        } catch (Exception $apiEx) {
            // If API fails due to timeout or other issues, log it but don't fail the operation
            error_log("[update_account] RTK API exception: " . $apiEx->getMessage());
            $apiMsg = 'RTK API timeout/error: ' . $apiEx->getMessage();
        }

        // Commit the transaction regardless of API result since local updates succeeded
        $db->commit();
        error_log("[update_account] Database transaction committed successfully");

        // finally, return the refreshed account record
        $refreshedAccount = $accountModel->getAccountById($accountId);

        // Build success message
        $successMessage = 'Account updated successfully in local database';
        if ($apiSuccess) {
            $successMessage .= ' and RTK API';
            if ($apiMsg) {
                $successMessage .= '. RTK Info: ' . $apiMsg;
            }
        } else {
            $successMessage .= ' (RTK API sync issue: ' . $apiMsg . ')';
        }

        // Log activity
        $adminId = $_SESSION['admin_id'] ?? null; // Get admin_id from session
        if ($adminId) {
            $logMessage = "Quản trị viên đã cập nhật tài khoản '{$input['username_acc']}' (ID Tài khoản: {$accountId})."; // Changed this line
            if ($apiMsg) {
                $logMessage .= " RTK Status: {$apiMsg}";
            }
            $logData = [
                ':user_id' => $rtkUserId, // ID of the user whose account is being modified (null if not found/specified)
                ':action' => 'account_updated_by_admin', // Specific action
                ':entity_type' => 'account',
                ':entity_id' => $accountId,
                ':notify_content' => $logMessage
            ];
            ActivityLogModel::addLog($db, $logData); // Call static method addLog
        } else {
            // This should ideally not be reached if Auth::ensureAuthorized() is effective.
            error_log("[update_account] Failed to log activity: Admin User ID not found in session. Auth issue?");
        }

        // Return success response
        api_success(['account' => $refreshedAccount], $successMessage);

    } catch (Exception $innerEx) {
        // Rollback transaction on any database error
        $db->rollback();
        error_log("[update_account] Transaction rolled back due to error: " . $innerEx->getMessage());
        throw $innerEx;
    }

} catch (PDOException $e) {
    error_log("Database error updating account: " 
        . $e->getMessage() 
        . "\nTrace: " . $e->getTraceAsString() 
        . "\nInput: " . print_r($input, true)
    );
    abort('Database error. Please check logs.', 500);
} catch (Exception $e) {
    error_log("Error updating account: " 
        . $e->getMessage() 
        . "\nTrace: " . $e->getTraceAsString()
    );
    abort($e->getMessage(), 500);
}
?>
