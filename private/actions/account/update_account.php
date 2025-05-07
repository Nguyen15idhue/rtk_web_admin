<?php
// filepath: private\actions\account\update_account.php
// Khởi bootstrap để có $db, BASE_PATH, BASE_URL
$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];
$base_path = $bootstrap['base_path'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');
// Basic security check
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'admin') {
    api_forbidden('Permission denied.');
}

require_once BASE_PATH . '/classes/AccountModel.php';
require_once BASE_PATH . '/api/rtk_system/account_api.php';

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

    $success = $accountModel->updateAccount($accountId, $updateData);

    if ($success) {
        // before touching registration, ensure we only update this account's registration
        $stmtRegId = $db->prepare("SELECT registration_id FROM survey_account WHERE id = ?");
        $stmtRegId->execute([$accountId]);
        $oldRegId = (int)$stmtRegId->fetchColumn();
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM survey_account WHERE registration_id = ?");
        $stmtCount->execute([$oldRegId]);
        if ((int)$stmtCount->fetchColumn() > 1) {
            // clone registration row
            $colsStmt = $db->query("SHOW COLUMNS FROM registration");
            $cols = [];
            while ($col = $colsStmt->fetch(PDO::FETCH_ASSOC)) {
                if ($col['Field'] === 'id') continue;
                $cols[] = $col['Field'];
            }
            $colsList = implode(',', $cols);
            $phList   = ':' . implode(',:', $cols);
            $rowStmt  = $db->prepare("SELECT $colsList FROM registration WHERE id = ?");
            $rowStmt->execute([$oldRegId]);
            $regData  = $rowStmt->fetch(PDO::FETCH_ASSOC);
            $insSql   = "INSERT INTO registration ($colsList) VALUES ($phList)";
            $insStmt  = $db->prepare($insSql);
            $insStmt->execute($regData);
            $newRegId = $db->lastInsertId();
            $updSa    = $db->prepare("UPDATE survey_account SET registration_id = ? WHERE id = ?");
            $updSa->execute([$newRegId, $accountId]);
            $regTargetId = $newRegId;
        } else {
            $regTargetId = $oldRegId;
        }

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

        // prepare payload for RTK API
        $apiPayload = [
            'id'              => $accountId,
            'name'            => $input['username_acc'],
            'userPwd'         => $currentPwd,
            'startTime'       => $startMs,
            'endTime'         => $endMs,
            'enabled'         => $updateData['enabled']      ?? 1,
            'numOnline'       => $updateData['concurrent_user'] ?? 1,
            'customerBizType' => $updateData['customerBizType'] ?? 1,
            'userId'          => $rtkUserId,
            'customerName'    => $customerName,
            'customerPhone'   => $customerPhone,
            'customerCompany' => $customerCompany,
            'casterIds'       => $casterIds,
            'regionIds'       => $regionIdsArr,
            'mountIds'        => $mountIds,
        ];
        // bỏ customerPhone khi rỗng để RTK API không validate
        if (empty($customerPhone)) {
            unset($apiPayload['customerPhone']);
        }
        error_log("[update_account] RTK API payload: " . print_r($apiPayload, true));

        // call external RTK update API
        $apiResult = updateRtkAccount($apiPayload);
        error_log("[update_account] RTK API result: " . print_r($apiResult, true));

        // capture the informational message
        $apiMsg = $apiResult['data']['msg'] ?? '';

        if (!$apiResult['success']) {
            $response['message'] .= ' External API Error: ' . $apiResult['error'];
            $response['success'] = false;
        }

        // finally, return the refreshed account record
        $response['account'] = $accountModel->getAccountById($accountId);

        // ensure we surface the RTK info in the final message
        $response['success'] = true;
        $response['message'] = 'Account updated successfully'
                            . ($apiMsg ? '. RTK Info: ' . $apiMsg : '');

    } else {
        $response['message'] = 'Failed to update account. Check logs for details.';
    }

    // debug: log full response before sending
    error_log("[update_account] response payload: " . json_encode($response));

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

if (!empty($response['success'])) {
    // wrap updated account under data.account
    api_success(['account' => $response['account'] ?? null], $response['message']);
} else {
    api_error($response['message'], 400);
}
?>
