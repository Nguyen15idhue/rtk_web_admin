<?php
header('Content-Type: application/json');

// Check SuperAdmin
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền.']);
    exit;
}

// Parse request
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
    exit;
}

// DB connection
$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $db->prepare("DELETE FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Xóa thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản để xóa.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . $e->getMessage()]);
}
