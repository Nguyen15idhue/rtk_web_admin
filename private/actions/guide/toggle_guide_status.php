<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id']) || !isset($_POST['status'])) {
        abort('ID and status are required', 400);
    }
    $model = new GuideModel();
    $ok = $model->toggleStatus($_POST['id'], $_POST['status']);
    echo json_encode(['success' => (bool)$ok]);
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [toggle_guide_status.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    abort('Error toggling status: '.$e->getMessage(), 500);
}
