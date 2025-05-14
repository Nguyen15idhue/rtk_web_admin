<?php
// filepath: private\actions\account\delete_account.php

// Thay include thủ công bằng bootstrap chung
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';
$db     = $config['db'];
$base   = $config['base_path'];

// Include the Auth class
require_once __DIR__ . '/../../classes/Auth.php';

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Use Auth class for authentication and authorization
Auth::ensureAuthorized('account_management');

// Get input data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!$input || !isset($input['id'])) {
    abort('Invalid input. Account ID is required.', 400);
}

$accountId = filter_var($input['id'], FILTER_SANITIZE_SPECIAL_CHARS);

if (!$db) {
    error_log("Database connection failed in delete_account.php");
    abort('Database connection error.', 500);
}

$accountModel = new AccountModel($db);

// --- Database Transaction (Optional but recommended) ---
$db->beginTransaction();

try {
    // Perform hard delete from database
    $success = $accountModel->hardDeleteAccount($accountId);

    if ($success) {
        // call centralized API function
        $apiResult = deleteRtkAccount([$accountId]);
        if (!$apiResult['success']) {
            throw new Exception('External API delete failed: ' . $apiResult['error']);
        }

        $db->commit();
        api_success(null, 'Account successfully marked for deletion.');
    } else {
        $db->rollBack();
        api_error('Failed to mark account for deletion in database.', 500);
    }
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error in delete_account.php: " . $e->getMessage());
    abort('An error occurred: ' . $e->getMessage(), 500);
}
