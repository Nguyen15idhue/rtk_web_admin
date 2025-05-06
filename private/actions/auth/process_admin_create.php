<?php

header('Content-Type: application/json');

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';

if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'admin') {
    api_error('Unauthorized', 401);
}

$conn      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$conn) {
    $conn = null;
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

// Check duplicate username
try {
    $stmt = $conn->prepare('SELECT id FROM admin WHERE admin_username = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        api_error('Username already exists', 400);
    }
} catch (PDOException $e) {
    error_log('DB Error (check admin_username) in process_admin_create: ' . $e->getMessage());
    api_error('Error checking username', 500);
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert new admin
try {
    $insert = 'INSERT INTO admin (name, admin_username, admin_password, role, created_at) VALUES (:name, :username, :password, :role, NOW())';
    $stmt = $conn->prepare($insert);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed);
    $stmt->bindParam(':role', $role);
    if ($stmt->execute()) {
        api_success([], 'Admin created successfully.');
    } else {
        api_error('Insert failed', 500);
    }
} catch (PDOException $e) {
    error_log('process_admin_create error: ' . $e->getMessage());
    api_error('Database error during insert', 500);
}
