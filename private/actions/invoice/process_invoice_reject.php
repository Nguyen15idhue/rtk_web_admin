<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
require_once __DIR__ . '/../../utils/functions.php';
require_once BASE_PATH . '/classes/InvoiceModel.php';
Auth::ensureAuthorized('invoice_management');

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
    $model->update($invoiceId, [
        'status' => 'rejected',
        'rejected_reason' => $reason
    ]);

    api_success(null, 'Invoice rejected thành công.');
} catch (PDOException $e) {
    error_log('Error in process_invoice_reject: ' . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    api_error('Database error.', 500);
}
