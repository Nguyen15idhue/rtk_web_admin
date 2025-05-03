<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');
header('Content-Type: application/json');
// Only Super Admin can create other admins/customercares
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'admin') {
    $currentRole = $_SESSION['admin_role'] ?? '(none)';
    echo json_encode([
        'success'      => false,
        'message'      => "Unauthorized"
    ]);
    exit;
}
$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn      = $bootstrap['db'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
// Parse JSON input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input format.']);
    exit;
}
$name = trim($input['name'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';
// Basic validation
if (!$name || !$username || !$password || !in_array($role, ['admin','customercare'])) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields.']);
    exit;
}
// Check duplicate username
try {
    $stmt = $conn->prepare('SELECT id FROM admin WHERE admin_username = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        exit;
    }
} catch (PDOException $e) {
    error_log('DB Error (check admin_username) in process_admin_create: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error checking username.']);
    exit;
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
        echo json_encode(['success' => false, 'message' => 'Insert failed.']);
    }
} catch (PDOException $e) {
    error_log('DB Error (insert admin) in process_admin_create: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error during insert.']);
    exit;
}
