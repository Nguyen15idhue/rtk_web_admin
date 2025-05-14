<?php
// filepath: private/actions/support/get_support_request_details.php
require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('support_management');
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/SupportRequestModel.php';
$model = new SupportRequestModel();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    api_response(null, 'Invalid ID', 400);
}
try {
    $data = $model->getById($id);
    if (!$data) {
        api_response(null, 'Not found', 404);
    }
    api_response($data, 'Fetched successfully');
} catch (Throwable $e) {
    error_log("Error fetching support request detail: {$e->getMessage()}\n{$e->getTraceAsString()}");
    api_response(null, 'Error fetching detail', 500);
}
