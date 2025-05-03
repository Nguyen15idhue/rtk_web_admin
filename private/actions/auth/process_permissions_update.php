<?php
header('Content-Type: application/json');
// Only Super Admin can update permissions
if (!isset($_SESSION['admin_id']) || ($_SESSION['admin_role'] ?? '') !== 'admin') {
    abort('Unauthorized', 401);
}

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$conn) {
    $conn = null;
});

// Read JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['role']) || !is_array($data['permissions'])) {
    abort('Invalid input', 400);
}
$role = $data['role'];
$validRoles = ['admin','customercare'];
if (!in_array($role, $validRoles)) {
    abort('Invalid role', 400);
}
$permissions = $data['permissions'];
try {
    $stmt = $conn->prepare('UPDATE role_permissions SET allowed = :allowed WHERE role = :role AND permission = :perm');
    foreach ($permissions as $perm => $allow) {
        $allowed = $allow ? 1 : 0;
        $stmt->bindParam(':allowed', $allowed, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':perm', $perm);
        $stmt->execute();
    }
    echo json_encode(['success' => true, 'message' => 'Permissions updated']);
} catch (Exception $e) {
    error_log('process_permissions_update error: ' . $e->getMessage());
    abort('Error updating permissions', 500);
}
