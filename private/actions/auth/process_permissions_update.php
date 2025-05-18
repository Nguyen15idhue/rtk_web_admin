<?php
header('Content-Type: application/json');

// Use Auth class for authentication and authorization

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('permission_management_edit');
$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

// Read JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['role']) || !is_array($data['permissions'])) {
    api_error('Dữ liệu đầu vào không hợp lệ', 400);
}
$role = $data['role'];
$permissions = $data['permissions'];
try {
    $stmt = $db->prepare('UPDATE role_permissions SET allowed = :allowed WHERE role = :role AND permission = :perm');
    foreach ($permissions as $perm => $allow) {
        $allowed = $allow ? 1 : 0;
        $stmt->bindParam(':allowed', $allowed, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':perm', $perm);
        $stmt->execute();
    }
    api_success([], 'Quyền đã được cập nhật');
} catch (Exception $e) {
    error_log('process_permissions_update error: ' . $e->getMessage());
    api_error('Lỗi cập nhật quyền', 500);
}
