<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/app_permissions.php'; // Include app permissions

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('permission_management_edit');
$db = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

// Read JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data) || empty($data['role_key']) || empty($data['role_name']) || !isset($data['permissions']) || !is_array($data['permissions'])) {
    api_error('Invalid input. Role key, role name, and permissions are required.', 400);
}

$role_key = trim($data['role_key']);
$role_name = trim($data['role_name']); // Though not stored directly in role_permissions, it's good to have
$selected_permissions = $data['permissions'];

// Validate role_key format (alphanumeric and underscores)
if (!preg_match('/^[a-z0-9_]+$/', $role_key)) {
    api_error('Invalid Role Key format. Only lowercase letters, numbers, and underscores are allowed.', 400);
}

if (empty($role_name)) {
    api_error('Role Name cannot be empty.', 400);
}

// Check if role_key already exists
try {
    $stmt_check = $db->prepare("SELECT 1 FROM role_permissions WHERE role = :role_key LIMIT 1");
    $stmt_check->bindParam(':role_key', $role_key);
    $stmt_check->execute();
    if ($stmt_check->fetchColumn()) {
        api_error("Role Key '{$role_key}' already exists.", 409); // 409 Conflict
    }

    // Get all defined application permissions
    $all_app_permissions = require __DIR__ . '/../../config/app_permissions.php';
    $all_permission_codes = array_keys($all_app_permissions);

    $db->beginTransaction();

    // Insert into custom_roles table
    $stmt_insert_custom_role = $db->prepare("INSERT INTO custom_roles (role_key, role_display_name) VALUES (:role_key, :role_display_name)");
    $stmt_insert_custom_role->bindParam(':role_key', $role_key);
    $stmt_insert_custom_role->bindParam(':role_display_name', $role_name);
    $stmt_insert_custom_role->execute();

    $stmt_insert_permissions = $db->prepare("INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :permission, :allowed)");

    foreach ($all_permission_codes as $perm_code) {
        $allowed = in_array($perm_code, $selected_permissions) ? 1 : 0;
        $stmt_insert_permissions->bindParam(':role', $role_key);
        $stmt_insert_permissions->bindParam(':permission', $perm_code);
        $stmt_insert_permissions->bindParam(':allowed', $allowed, PDO::PARAM_INT);
        $stmt_insert_permissions->execute();
    }

    $db->commit();
    api_success(['role_key' => $role_key], 'Quyền mới \'' . $role_name . ' (' . $role_key . ')\' đã được tạo.');

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('process_role_create error: ' . $e->getMessage());
    api_error('Error creating new role: ' . $e->getMessage(), 500);
}
?>
