<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
header('Content-Type: application/json');

// Kiểm tra quyền truy cập
Auth::ensureAuthorized('guide_management_edit');

try {
    $model = new GuideModel();
    $topics = $model->getDistinctTopics();
    // Trả về danh sách chủ đề
    api_success($topics, 'Danh sách chủ đề lấy thành công');
} catch (\Throwable $e) {
    error_log("Error fetching topics: " . $e->getMessage());
    api_error('Lỗi khi lấy danh sách chủ đề', 500);
}
