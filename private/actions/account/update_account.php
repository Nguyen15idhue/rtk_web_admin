<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\update_account.php
header('Content-Type: application/json');

// Basic security check
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php';
require_once __DIR__ . '/../../api/rtk_system/account_api.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        $input = $_POST;
    }
    $accountId = $input['id'] ?? null;

    // Basic validation
    if (!$accountId || empty($input['username_acc'])) {
        $response['message'] = 'Missing required fields (Account ID, Username).';
        echo json_encode($response);
        exit;
    }

    // --- XỬ LÝ TRẠNG THÁI ---
    $inputStatus = $input['status'] ?? null;
    $regStatus = in_array($inputStatus, ['pending','rejected']) 
                 ? $inputStatus 
                 : 'active';

    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!$db) {
            throw new Exception("Database connection failed.");
        }

        $accountModel = new AccountModel($db);

        // Check if username already exists (excluding the current account)
        if ($accountModel->usernameExists($input['username_acc'], $accountId)) {
             $response['message'] = 'Username already exists.';
             echo json_encode($response);
             exit;
        }

        // Prepare data for update (filter out ID)
        $updateData = $input;
        unset($updateData['id']); // Don't try to update the ID itself

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
            // Cập nhật registration.status
            if ($inputStatus !== null) {
                $stmtReg = $db->prepare("
                    UPDATE registration 
                    SET status = ?, updated_at = NOW() 
                    WHERE id = (
                        SELECT registration_id FROM survey_account WHERE id = ?
                    )
                ");
                $stmtReg->execute([$regStatus, $accountId]);
            }

            $response['success'] = true;
            $response['message'] = 'Account updated successfully.';

            // --- New: Ghi đè start_time và end_time vào bảng registration ---
            $activation = $input['activation_date'] ?? null;
            $expiry     = $input['expiry_date'] ?? null;
            if ($activation && $expiry) {
                $stmtReg = $db->prepare("
                    UPDATE registration 
                    SET start_time = ?, end_time = ?
                    WHERE id = (
                        SELECT registration_id 
                        FROM survey_account 
                        WHERE id = ?
                    )
                ");
                $stmtReg->execute([
                    $activation . ' 00:00:00', 
                    $expiry     . ' 23:59:59', 
                    $accountId
                ]);
            }

            // fetch existing password if no new password provided
            if (empty($input['password_acc'])) {
                $stmtPwd = $db->prepare("SELECT password_acc FROM survey_account WHERE id = ?");
                $stmtPwd->execute([$accountId]);
                $currentPwd = $stmtPwd->fetchColumn();
            } else {
                $currentPwd = $input['password_acc'];
            }

            // --- New: preserve mountIds if location_id unchanged ---
            $stmtOldLoc = $db->prepare("
                SELECT r.location_id 
                FROM registration r 
                JOIN survey_account sa ON sa.registration_id = r.id 
                WHERE sa.id = ?
            ");
            $stmtOldLoc->execute([$accountId]);
            $oldLocationId = (int)$stmtOldLoc->fetchColumn();
            $newLocationId = isset($input['location_id']) ? (int)$input['location_id'] : $oldLocationId;
            // nếu đổi location thì dùng mới, ngược lại giữ cũ
            $locationIdToUse = ($newLocationId !== $oldLocationId) ? $newLocationId : $oldLocationId;
            $mountIds = getMountPointsByLocationId($locationIdToUse);

            // prepare payload for RTK API, thêm mountIds
            $apiPayload = [
                'id'              => $accountId,
                'name'            => $input['username_acc'],
                'userPwd'         => $currentPwd,
                'startTime'       => strtotime($input['activation_date']) * 1000,
                'endTime'         => strtotime($input['expiry_date'])   * 1000,
                'enabled'         => $updateData['enabled']      ?? 1,
                'numOnline'       => $updateData['concurrent_user'] ?? 1,
                'customerBizType' => $updateData['customerBizType'] ?? 1,
                'mountIds'        => $mountIds,
                // add casterIds, regionIds, mountIds, customerCompany if needed
            ];

            // call external RTK update API
            $apiResult = updateRtkAccount($apiPayload);
            if (!$apiResult['success']) {
                // append lỗi từ API bên ngoài vào message để front‑end show lên
                $response['message'] .= ' External API Error: ' . $apiResult['error'];
                // nếu muốn coi là không thành công tổng thể
                $response['success'] = false;
            }

            // finally, return the refreshed account record
            $response['account'] = $accountModel->getAccountById($accountId);

        } else {
            $response['message'] = 'Failed to update account. Check logs for details.';
        }

    } catch (PDOException $e) {
        error_log("Database error updating account: " . $e->getMessage());
        $response['message'] = 'Database error. Please check logs.';
    } catch (Exception $e) {
        error_log("Error updating account: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
