<?php
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

// Determine user ID: input or current admin
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$userId = $input['user_id'] ?? $_SESSION['admin_id'];

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) {
        throw new Exception('Database connection failed.');
    }
    $rtk = new AccountModel($db);
    $accounts = $rtk->getAccountsByUserId($userId);

    echo json_encode(['success' => true, 'accounts' => $accounts]);
} catch (Exception $e) {
    error_log('Error fetching accounts via RtkAccount: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching accounts.']);
}
