<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    api_error('Guide ID is required', 400);
}

try {
    $model = new GuideModel();
    $data = $model->getOne((int)$_GET['id']) ?: [];
    api_success($data);
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [get_guide_details.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error fetching details: ' . $e->getMessage(), 500);
}
