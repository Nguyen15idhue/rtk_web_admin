<?php
header('Content-Type: application/json');

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('permission_management_view');
$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

try {
    // 1. Read and validate role parameter
    $role = $_GET['role'] ?? '';
    if (empty($role)) {
        api_error('Role parameter is required', 400);
    }

    // Fetch permissions for this role
    $stmt = $db->prepare('SELECT permission, allowed FROM role_permissions WHERE role = :role');
    $stmt->execute([':role' => $role]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no entries, initialize defaults
    if (empty($rows)) {
        // Define default permissions
        $default = ['dashboard'=>1, 'user_management'=>0, 'user_create'=>0, 'settings'=>0];
        $insert = $db->prepare('INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :perm, :allowed)');
        foreach ($default as $perm => $allow) {
            $insert->execute([':role' => $role, ':perm' => $perm, ':allowed' => $allow]);
        }
        // re-fetch
        $stmt->execute([':role' => $role]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    api_success($rows);
} catch (Exception $e) {
    error_log('fetch_permissions error: ' . $e->getMessage());
    api_error('Error fetching permissions', 500);
}
