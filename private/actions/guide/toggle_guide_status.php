<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized(['admin']); // Only admins can toggle guide status

header('Content-Type: application/json');

try {
    // Đọc raw JSON nếu gửi Content-Type: application/json
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?: $_POST;

    $id     = $input['id']     ?? null;
    $status = $input['status'] ?? null;

    if (empty($id) || !isset($status)) {
        api_error('ID and status are required', 400);
    }

    $model = new GuideModel();
    $ok = $model->toggleStatus($id, $status);

    if ($ok) {
        api_success([], 'Guide status toggled successfully');
    } else {
        api_error('Error toggling guide status', 500);
    }
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [toggle_guide_status.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error toggling status: ' . $e->getMessage(), 500);
}
