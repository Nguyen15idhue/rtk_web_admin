<?php
header('Content-Type: application/json');

$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

// Basic security check
if (!isset($_SESSION['admin_id'])) {
    abort('Unauthorized', 401);
    exit;
}

// Determine user ID: input or current admin
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$userId = $input['user_id'] ?? $_SESSION['admin_id'];

if (!$userId) {
    abort('User ID is required.', 400);
    exit;
}

try {
    if (!$db) {
        throw new Exception('Database connection failed.');
    }
    $rtk = new AccountModel($db);
    $accounts = $rtk->getAccountsByUserId($userId);

    echo json_encode(['success' => true, 'accounts' => $accounts]);
} catch (Exception $e) {
    error_log('Error fetching accounts via RtkAccount: ' . $e->getMessage());
    abort('Error fetching accounts.', 500);
}
?>
