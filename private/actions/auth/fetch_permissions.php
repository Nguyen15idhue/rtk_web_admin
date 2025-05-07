<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class

// Use Auth class for authentication and authorization
Auth::ensureAuthorized(['admin']);

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$role = $_GET['role'] ?? '';
$validRoles = ['admin','customercare'];
if (!in_array($role, $validRoles)) {
    api_error('Invalid role', 400);
    exit;
}

try {
    // Fetch permissions
    $stmt = $db->prepare('SELECT permission, allowed FROM role_permissions WHERE role = :role');
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // If no entries, initialize defaults
    if (empty($rows)) {
        // Define default permissions
        $default = ['dashboard'=>1, 'user_management'=>0, 'user_create'=>0, 'settings'=>0];
        $insert = $db->prepare('INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :perm, :allowed)');
        foreach ($default as $perm => $allow) {
            $insert->bindParam(':role', $role);
            $insert->bindParam(':perm', $perm);
            $insert->bindParam(':allowed', $allow, PDO::PARAM_INT);
            $insert->execute();
        }
        // re-fetch
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    api_success($rows);
} catch (Exception $e) {
    error_log('fetch_permissions error: ' . $e->getMessage());
    api_error('Error fetching permissions', 500);
}
