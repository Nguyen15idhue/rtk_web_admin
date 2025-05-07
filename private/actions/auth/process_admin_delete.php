<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized(['admin']); // Only admins can delete other admins

// Parse request
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id || !is_numeric($id)) {
    api_error('Invalid ID', 400);
}

// DB connection
$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $db->prepare("DELETE FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        api_success([], 'Xóa thành công.');
    } else {
        api_error('Không tìm thấy tài khoản để xóa.', 400);
    }
} catch (PDOException $e) {
    error_log('process_admin_delete error: ' . $e->getMessage());
    api_error('Error deleting admin', 500);
}
