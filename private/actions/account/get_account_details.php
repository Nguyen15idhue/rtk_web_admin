<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\get_account_details.php

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
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request', 'account' => null];

if (isset($_GET['id'])) {
    $accountId = $_GET['id'];

    try {
        if (!$db) {
            throw new Exception("Database connection failed.");
        }

        $accountModel = new AccountModel($db);
        $account = $accountModel->getAccountById($accountId);

        if ($account) {
            // Optionally format dates or other fields before sending
            // Example: $account['created_at_formatted'] = format_date($account['created_at']);
            $response['success'] = true;
            $response['message'] = 'Account details fetched successfully.';
            $response['account'] = $account;
        } else {
            $response['message'] = 'Account not found.';
        }

    } catch (PDOException $e) {
        error_log("Database error fetching account details: " . $e->getMessage());
        $response['message'] = 'Database error. Please check logs.';
    } catch (Exception $e) {
        error_log("Error fetching account details: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Account ID not provided.';
}

echo json_encode($response);
