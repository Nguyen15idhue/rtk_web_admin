<?php

header('Content-Type: application/json');
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('permission_management_edit'); // Only admins can create other admins
$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Phương thức yêu cầu không hợp lệ', 405);
}

// Parse JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    api_error('Định dạng đầu vào không hợp lệ', 400);
}

$name = trim($input['name'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';

// Basic validation
if (!$name || !$username || !$password) {
    api_error('Thiếu trường hoặc trường không hợp lệ', 400);
}

require_once __DIR__ . '/../../classes/AdminModel.php';
$model = new AdminModel();

// Check duplicate username via model
if ($model->getByUsername($username)) {
    api_error('Username đã tồn tại', 400);
}

// Delegate to model
if ($model->create([
    'name'     => $name,
    'username' => $username,
    'password' => $password,
    'role'     => $role
])) {
    api_success([], 'Tạo tài khoản quản trị thành công.');
} else {
    api_error('Insert failed', 500);
}
