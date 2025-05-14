<?php
header('Content-Type: application/json');

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('permission_management'); // Only admins can update other admins
$db        = $bootstrap['db'];

// Ensure PDO is closed when the script ends
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$input = json_decode(file_get_contents('php://input'), true);

// Get and validate input data
$id       = isset($input['id']) ? (int)$input['id'] : 0;
$name     = trim($input['name'] ?? '');
$password = $input['password'] ?? '';
$role     = $input['role'] ?? '';

if (!$id || !$name || !in_array($role, ['admin','customercare'])) {
    api_error('Invalid data', 400);
}

require_once __DIR__ . '/../../classes/AdminModel.php';
$model = new AdminModel();

try {
    $changed = $model->update($id, [
        'name'     => $name,
        'password' => $password,
        'role'     => $role
    ]);
    if ($changed) {
        api_success([], 'Cập nhật thành công.');
    } else {
        api_success([], 'Không có thay đổi (ID không tồn tại hoặc dữ liệu giống trước).');
    }
} catch (PDOException $e) {
    error_log('process_admin_update error: ' . $e->getMessage());
    api_error('Lỗi khi cập nhật', 500);
}
