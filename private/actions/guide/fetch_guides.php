<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');

try {
    $model = new GuideModel();
    $list = $model->getAll($_GET['search'] ?? '');
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
