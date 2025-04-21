<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\update_account.php
header('Content-Type: application/json');
session_start();

// Basic security check
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php';

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
            $response['success'] = true;
            $response['message'] = 'Account updated successfully.';
            // Optionally fetch and return the updated account details
            // $response['account'] = $accountModel->getAccountById($accountId);
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
