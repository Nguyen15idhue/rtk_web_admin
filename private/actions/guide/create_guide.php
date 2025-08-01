<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
// Load Cloudinary service for file uploads
require_once __DIR__ . '/../../services/CloudinaryService.php';
header('Content-Type: application/json');

Auth::ensureAuthorized('guide_management_edit');

try {
    // handle file upload via Cloudinary
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $result = CloudinaryService::uploadRaw($_FILES['thumbnail']['tmp_name'], ['folder' => 'rtk_web_admin/guide']);
        $_POST['thumbnail'] = $result['secure_url'] ?? '';
    }

    // prepare data
    $data = $_POST;
    $data['author_id'] = $_SESSION['admin_id'];

    $model = new GuideModel();
    $ok = $model->create($data);
    if ($ok) {
        api_success([], 'Guide created successfully');
    } else {
        api_error('Error creating guide', 500);
    }
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [create_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error creating guide: ' . $e->getMessage(), 500);
}
