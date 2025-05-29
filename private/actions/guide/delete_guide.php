<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('guide_management_edit');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    api_error('ID không hợp lệ', 400);
}

try {
    $model = new GuideModel();
    
    // Check if guide exists
    $guide = $model->getOne($id);
    if (!$guide) {
        api_error('Hướng dẫn không tồn tại', 404);
    }
    
    // Delete guide
    $result = $model->delete($id);
    
    if ($result) {
        api_success(['message' => 'Xóa hướng dẫn thành công']);
    } else {
        api_error('Không thể xóa hướng dẫn', 500);
    }
    
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [delete_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Lỗi xóa hướng dẫn: ' . $e->getMessage(), 500);
}
