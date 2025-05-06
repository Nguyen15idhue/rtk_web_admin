<?php
// filepath: private\actions\account\get_account_details.php

// Khởi bootstrap
$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Basic security check (adjust as needed)
if (!isset($_SESSION['admin_id'])) {
    abort('Unauthorized', 401);
}

if (!isset($_GET['id'])) {
    abort('Account ID not provided.', 400);
}

$accountId = $_GET['id'];

try {
    $accountModel = new AccountModel($db);
    $account = $accountModel->getAccountById($accountId);

    if ($account) {
        // envelope.data = the account object
        api_success($account, 'Account details fetched successfully.');
    } else {
        api_error('Account not found.', 404);
    }
} catch (PDOException $e) {
    error_log("Database error fetching account details: " . $e->getMessage());
    api_error('Database error. Please check logs.', 500);
} catch (Exception $e) {
    error_log("Error fetching account details: " . $e->getMessage());
    api_error('Internal error.', 500);
}
// note: api_success / api_error will exit after sending JSON
