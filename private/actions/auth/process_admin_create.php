<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');
header('Content-Type: application/json');

function abort($message, $statusCode) {
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Only Super Admin can create other admins/customercares
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'admin') {
    abort('Unauthorized', 401);
}

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$conn) {
    $conn = null;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort('Invalid request method', 405);
}

// Parse JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    abort('Invalid input format', 400);
}

$name = trim($input['name'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';

// Basic validation
if (!$name || !$username || !$password || !in_array($role, ['admin','customercare'])) {
    abort('Missing or invalid fields', 400);
}

// Check duplicate username
try {
    $stmt = $conn->prepare('SELECT id FROM admin WHERE admin_username = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        abort('Username already exists', 400);
    }
} catch (PDOException $e) {
    error_log('DB Error (check admin_username) in process_admin_create: ' . $e->getMessage());
    abort('Error checking username', 500);
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
        echo json_encode(['success' => true, 'message' => 'Admin created successfully.']);
    } else {
        abort('Insert failed', 500);
    }
} catch (PDOException $e) {
    error_log('DB Error (insert admin) in process_admin_create: ' . $e->getMessage());
    abort('Database error during insert', 500);
}
