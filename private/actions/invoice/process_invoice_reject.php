<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../utils/functions.php';
require_once BASE_PATH . '/classes/InvoiceModel.php';
require_once __DIR__ . '/../../classes/ActivityLogModel.php'; // Added for ActivityLogModel
Auth::ensureAuthorized('invoice_review_edit');

$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$input = json_decode(file_get_contents('php://input'), true);
$invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id'] : 0;
$reason    = isset($input['reason'])     ? trim($input['reason']) : '';

if ($invoiceId <= 0 || $reason === '') {
    api_error('Invalid invoice ID or reason.', 400);
}

try {
    $model = new InvoiceModel();
    $invoice = $model->getOne($invoiceId); // Use getOne() instead of getById()
    if (!$invoice) {
        api_error('Invoice not found.', 404);
        return;
    }

    // Get customer ID via model
    $customerId = $model->getCustomerId($invoiceId);

    $model->update($invoiceId, [
        'status' => 'rejected',
        'rejected_reason' => $reason
    ]);

    // Activity log using ActivityLogModel
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'     => $customerId, // Use fetched customerId
            ':action'      => 'reject_invoice',
            ':entity_type' => 'invoice',
            ':entity_id'   => $invoiceId,
            ':old_values'  => json_encode(['status' => $invoice['status']]),
            ':new_values'  => json_encode(['status' => 'rejected', 'reason' => $reason, 'customer_id' => $customerId]),
            ':notify_content' => "Yêu cầu xuất hoá đơn #{$invoiceId} đã bị từ chối. Lý do: {$reason}"
        ]
    );

    api_success(null, 'Invoice rejected thành công.');
} catch (PDOException $e) {
    error_log('Error in process_invoice_reject: ' . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    api_error('Database error.', 500);
}
