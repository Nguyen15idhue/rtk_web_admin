<?php
header('Content-Type: application/json');
// Only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn      = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$conn) {
    $conn = null;
});

$role = $_GET['role'] ?? '';
$validRoles = ['admin','customercare'];
if (!in_array($role, $validRoles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

try {
    // Fetch permissions
    $stmt = $conn->prepare('SELECT permission, allowed FROM role_permissions WHERE role = :role');
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // If no entries, initialize defaults
    if (empty($rows)) {
        // Define default permissions
        $default = ['dashboard'=>1, 'user_management'=>0, 'user_create'=>0, 'settings'=>0];
        $insert = $conn->prepare('INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :perm, :allowed)');
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
    echo json_encode(['success'=>true, 'data'=>$rows]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'message'=>'Error fetching permissions']);
}
