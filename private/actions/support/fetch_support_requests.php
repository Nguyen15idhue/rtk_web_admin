<?php
// filepath: private/actions/support/fetch_support_requests.php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('support_management_view');
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/SupportRequestModel.php';
$model = new SupportRequestModel();
$filters = [
    'search'   => trim($_GET['search'] ?? ''),
    'status'   => trim($_GET['status'] ?? ''),
    'category' => trim($_GET['category'] ?? ''),
];
try {
    $data = $model->getAll($filters);
    api_response($data, 'Fetched successfully');
} catch (Throwable $e) {
    error_log("Error fetching support requests: {$e->getMessage()}\n{$e->getTraceAsString()}");
    api_response(null, 'Error fetching support requests', 500, ['exception' => $e->getMessage()]);
}
