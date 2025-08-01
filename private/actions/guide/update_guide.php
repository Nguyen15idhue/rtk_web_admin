<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
// Load Cloudinary service for file uploads
require_once __DIR__ . '/../../services/CloudinaryService.php';
Auth::ensureAuthorized('guide_management_edit');

header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        abort('Guide ID is required', 400);
    }

    // file upload via Cloudinary
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $result = CloudinaryService::uploadRaw($_FILES['thumbnail']['tmp_name'], ['folder' => 'rtk_web_admin/guide']);
        $_POST['thumbnail'] = $result['secure_url'] ?? '';
    } else {
        // giữ ảnh cũ
        $_POST['thumbnail'] = $_POST['existing_thumbnail'] ?? '';
        unset($_POST['existing_thumbnail']);
    }

    $model = new GuideModel();
    $ok = $model->update($_POST['id'], $_POST);
    if ($ok) {
        api_success([], 'Guide updated successfully');
    } else {
        api_error('Error updating guide', 500);
    }
} catch (\Throwable $e) {
    // Bổ sung logging chi tiết
    error_log(sprintf(
        "Critical [update_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error updating guide: ' . $e->getMessage(), 500);
}
