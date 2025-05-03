<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');

try {
    $model = new GuideModel();
    $list = $model->getAll($_GET['search'] ?? '');
    echo json_encode($list);
} catch (\Throwable $e) {
    // Bổ sung logging chi tiết
    error_log(sprintf(
        "Critical [fetch_guides.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    abort('Error fetching guides: '.$e->getMessage(), 500);
}
