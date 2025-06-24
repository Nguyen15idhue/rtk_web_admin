<?php
// filepath: private/actions/support/update_support_request.php
require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('support_management_edit');
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/SupportRequestModel.php';
require_once __DIR__ . '/../../classes/ActivityLogModel.php'; // Added for ActivityLogModel
$model = new SupportRequestModel();
$db = Database::getInstance()->getConnection(); // Get DB instance for logging

// Expect JSON body with id, status, admin_response
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['id'], $input['status'], $input['admin_response'])) {
    api_response(null, 'Invalid input', 400);
}
$id = (int)$input['id'];
$status = trim($input['status']);
$response = trim($input['admin_response']);

// Get localized status text for email notification
$statusMaps = require __DIR__ . '/../../config/status_badge_maps.php';
$localizedStatus = $statusMaps['support'][$status]['text'] ?? $status;

if ($id <= 0) {
    api_response(null, 'Invalid ID', 400);
}

// Get old status for logging
$oldRequest = $model->getById($id);
$oldStatus = $oldRequest ? $oldRequest['status'] : null;
$userId = $oldRequest ? (int)$oldRequest['user_id'] : null;

try {
    $success = $model->update($id, $status, $response);
    if ($success) {
        // Log activity
        ActivityLogModel::addLog(
            $db,
            [
                ':user_id'      => $userId, // User who submitted the request
                ':action'       => 'support_request_updated',
                ':entity_type'  => 'support_request',
                ':entity_id'    => $id,
                ':old_values'   => json_encode(['status' => $oldStatus, 'admin_response' => $oldRequest['admin_response'] ?? null]),
                ':new_values'   => json_encode(['status' => $status, 'admin_response' => $response]),
                ':notify_content'  => "Yêu cầu hỗ trợ #{$id} đã được cập nhật trạng thái thành \"{$localizedStatus}\"."
            ]
        );
        api_response(null, 'Updated successfully');
    } else {
        api_response(null, 'Update failed', 500);
    }
} catch (Throwable $e) {
    error_log("Error updating support request: {$e->getMessage()}\n{$e->getTraceAsString()}");
    api_response(null, 'Update error', 500);
}
