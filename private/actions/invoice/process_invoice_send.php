<?php
declare(strict_types=1);

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php'; 
require_once BASE_PATH . '/classes/InvoiceModel.php';
require_once BASE_PATH . '/classes/ActivityLogModel.php'; // Corrected path for ActivityLogModel
Auth::ensureAuthorized('invoice_review_edit');

// Detect AJAX (XHR) requests for JSON response
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort('Method Not Allowed.', 405);
}

$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

$invoiceId = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;
if ($invoiceId <= 0 || empty($_FILES['invoice_file'])) {
    abort('Invalid request.', 400);
}

$file = $_FILES['invoice_file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    abort('Upload error.', 400);
}
if (mime_content_type($file['tmp_name']) !== 'application/pdf') {
    abort('Invalid file type.', 400);
}

require_once __DIR__ . '/../../services/CloudinaryService.php';
// Upload PDF invoice to Cloudinary
    $result = CloudinaryService::uploadRaw($file['tmp_name'], [
        'folder' => 'rtk_web_admin/invoice'
    ]);
    $fileName = $result['secure_url'] ?? '';
    if (empty($fileName)) {
        abort('Failed to upload file to cloud storage.', 500);
    }

try {
    $model = new InvoiceModel();
    $invoice = $model->getOne($invoiceId); // Get current invoice details

    if (!$invoice) {
        abort('Invoice not found.', 404);
    }

    $oldStatus = $invoice['status']; // Capture old status
    $customerId = $model->getCustomerId($invoiceId); // Fetch customerId via model

    $model->attachFile($invoiceId, $fileName);

    // Log activity
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'      => $customerId, // Use fetched customerId
            ':action'       => 'invoice_sent',
            ':entity_type'  => 'invoice',
            ':entity_id'    => $invoiceId,
            ':old_values'   => json_encode(['status' => $oldStatus]),
            ':new_values'   => json_encode(['status' => 'approved', 'file' => $fileName, 'customer_id' => $customerId]), // Assuming 'approved' is the new status
            ':notify_content'  => "Hóa đơn #{$invoiceId} đã được gửi." // Ensure description is passed
        ]
    );

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Upload thành công.',
            'fileName' => $fileName
        ]);
        exit;
    }

    header('Location: ' . $bootstrap['base_url'] . 'public/pages/invoice/invoice_review.php');
    exit;
} catch (PDOException $e) {
    error_log('Error in process_invoice_send: ' . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    abort('Database error.', 500);
}
