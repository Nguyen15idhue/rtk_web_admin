<?php
// filepath: private\actions\account\get_account_details.php

// Khởi bootstrap
$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db        = $bootstrap['db'];

require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Use Auth class for authentication
Auth::ensureAuthenticated(); // Allow any authenticated user to get details, or restrict further if needed.

if (!isset($_GET['id'])) {
    abort('Account ID not provided.', 400);
}

$accountId = $_GET['id'];

try {
    $accountModel = new AccountModel($db);
    $account = $accountModel->getAccountById($accountId);

    if ($account) {
        // Fetch all mountpoints for this account's location
        $locationId = $account['location_id'] ?? null;
        if ($locationId) {
            $stmt = $db->prepare("
                SELECT id, ip, port, mountpoint 
                FROM mount_point 
                WHERE location_id = ?
            ");
            $stmt->execute([$locationId]);
            $account['mountpoints'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $account['mountpoints'] = [];
        }

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
