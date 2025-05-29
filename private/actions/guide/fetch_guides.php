<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('guide_management_view');

header('Content-Type: application/json');

try {
    $model = new GuideModel();
    $list = $model->getAll($_GET['search'] ?? '', $_GET['topic'] ?? '', $_GET['status'] ?? '');
    api_success($list);
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [fetch_guides.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error fetching guides: ' . $e->getMessage(), 500);
}
