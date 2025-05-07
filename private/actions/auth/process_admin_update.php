<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized(['admin']); // Only admins can update other admins

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
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

// Build SQL query
if ($password !== '') {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE admin SET name = :name, admin_password = :pwd, role = :role WHERE id = :id";
} else {
    $sql = "UPDATE admin SET name = :name, role = :role WHERE id = :id";
}

try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($password !== '') {
        $stmt->bindParam(':pwd', $hashed);
    }
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        api_success([], 'Cập nhật thành công.');
    } else {
        api_success([], 'Không có thay đổi (ID không tồn tại hoặc dữ liệu giống trước).');
    }
} catch (PDOException $e) {
    error_log('process_admin_update error: ' . $e->getMessage());
    api_error('Lỗi khi cập nhật', 500);
}
