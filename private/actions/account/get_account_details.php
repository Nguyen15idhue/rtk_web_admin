<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\get_account_details.php
header('Content-Type: application/json');
session_start();

// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

// Basic security check (adjust as needed)
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php';

$response = ['success' => false, 'message' => 'Invalid request', 'account' => null];

if (isset($_GET['id'])) {
    $accountId = $_GET['id'];

    try {
        $database = new Database();
        $db = $database->getConnection();

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
