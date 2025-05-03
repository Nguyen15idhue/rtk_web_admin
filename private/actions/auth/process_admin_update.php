<?php
header('Content-Type: application/json');

// Chỉ SuperAdmin được phép
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền thực hiện hành động này.']);
    exit;
}

$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$input = json_decode(file_get_contents('php://input'), true);

// Lấy và kiểm tra dữ liệu đầu vào
$id       = isset($input['id']) ? (int)$input['id'] : 0;
$name     = trim($input['name'] ?? '');
$password = $input['password'] ?? '';
$role     = $input['role'] ?? '';

if (!$id || !$name || !in_array($role, ['admin','customercare'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

// Xây dựng truy vấn
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
        echo json_encode(['success' => true, 'message' => 'Cập nhật tài khoản thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không có thay đổi (ID không tồn tại hoặc dữ liệu giống trước).']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()]);
}
