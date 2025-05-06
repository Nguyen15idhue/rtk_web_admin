<?php
// filepath: private\actions\account\delete_account.php

// Thay include thủ công bằng bootstrap chung
$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db     = $config['db'];
$base   = $config['base_path'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}


// Get input data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!$input || !isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Account ID is required.']);
    exit;
}

$accountId = filter_var($input['id'], FILTER_SANITIZE_SPECIAL_CHARS);

if (!$db) {
    error_log("Database connection failed in delete_account.php");
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

$accountModel = new AccountModel($db);

// --- Database Transaction (Optional but recommended) ---
$db->beginTransaction();

try {
    // Perform soft delete by setting deleted_at
    $success = $accountModel->deleteAccount($accountId);

    if ($success) {
        // call centralized API function
        $apiResult = deleteRtkAccount([$accountId]);
        if (!$apiResult['success']) {
            throw new Exception('External API delete failed: '.$apiResult['error']);
        }

        // Log activity (implement logging function)
        // log_activity($_SESSION['admin_id'], 'delete', 'survey_account', $accountId, null, null);

        $db->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Account successfully marked for deletion.'
        ]);
    } else {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to mark account for deletion in database.']);
    }
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error in delete_account.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
