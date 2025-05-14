<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized('admin_user_create'); // Only admins can create other admins

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

$db      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method', 405);
}

// Parse JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    api_error('Invalid input format', 400);
}

$name = trim($input['name'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';

// Basic validation
if (!$name || !$username || !$password || !in_array($role, ['admin','customercare'])) {
    api_error('Missing or invalid fields', 400);
}

require_once __DIR__ . '/../../classes/AdminModel.php';
$model = new AdminModel();

// Check duplicate username via model
if ($model->getByUsername($username)) {
    api_error('Username already exists', 400);
}

// Delegate to model
if ($model->create([
    'name'     => $name,
    'username' => $username,
    'password' => $password,
    'role'     => $role
])) {
    api_success([], 'Admin created successfully.');
} else {
    api_error('Insert failed', 500);
}
