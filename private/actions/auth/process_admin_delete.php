<?php
session_start();
header('Content-Type: application/json');

// Check SuperAdmin
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin') {
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
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
$db = (new Database())->getConnection();
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
