<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';

if (!isset($_GET['id'])) {
    abort('Guide ID is required', 400);
}

header('Content-Type: application/json');
try {
    $model = new GuideModel();
    $data = $model->getOne((int)$_GET['id']) ?: [];
    echo json_encode($data);
} catch (\Throwable $e) {
    // Bổ sung logging chi tiết
    error_log(sprintf(
        "Critical [get_guide_details.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    abort('Error fetching details: '.$e->getMessage(), 500);
}
