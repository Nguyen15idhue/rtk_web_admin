<?php
header('Content-Type: application/json');

// Use Auth class for authentication and authorization

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('permission_edit');
$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

// Read JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['role']) || !is_array($data['permissions'])) {
    api_error('Invalid input', 400);
}
$role = $data['role'];
$validRoles = ['admin','customercare'];
if (!in_array($role, $validRoles)) {
    api_error('Invalid role', 400);
}
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
    api_success([], 'Permissions updated');
} catch (Exception $e) {
    error_log('process_permissions_update error: ' . $e->getMessage());
    api_error('Error updating permissions', 500);
}
