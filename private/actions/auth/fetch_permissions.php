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
        // Updated: Set to empty array as base permissions are removed by the new SQL.
        // New roles will start with no permissions by default.
        $default = [];
        if (!empty($default)) { // Only proceed if there are defaults to insert
            $insert = $db->prepare('INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :perm, :allowed)');
            foreach ($default as $perm => $allow) {
                $insert->execute([':role' => $role, ':perm' => $perm, ':allowed' => $allow]);
            }
            // re-fetch
            $stmt->execute([':role' => $role]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // If $default is empty, $rows is already empty and correct.
            // No need to re-fetch, $rows remains an empty array.
        }
    }
    api_success($rows);
} catch (Exception $e) {
    error_log('fetch_permissions error: ' . $e->getMessage());
    api_error('Error fetching permissions', 500);
}
