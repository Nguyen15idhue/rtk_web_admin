<?php
// filepath: private/actions/support/update_support_request.php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('support_management_edit');
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/SupportRequestModel.php';
$model = new SupportRequestModel();

// Expect JSON body with id, status, admin_response
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id'], $input['status'], $input['admin_response'])) {
    api_response(null, 'Invalid input', 400);
}
$id = (int)$input['id'];
$status = trim($input['status']);
$response = trim($input['admin_response']);

if ($id <= 0) {
    api_response(null, 'Invalid ID', 400);
}
try {
    $success = $model->update($id, $status, $response);
    if ($success) {
        api_response(null, 'Updated successfully');
    } else {
        api_response(null, 'Update failed', 500);
    }
} catch (Throwable $e) {
    error_log("Error updating support request: {$e->getMessage()}\n{$e->getTraceAsString()}");
    api_response(null, 'Update error', 500);
}
