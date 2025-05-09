<?php
declare(strict_types=1);

require_once __DIR__ . '/../../core/page_bootstrap.php'; // đã include error_handler
require_once __DIR__ . '/../../classes/Auth.php';
require_once BASE_PATH . '/classes/InvoiceModel.php';    // thêm
Auth::ensureAuthorized(['admin','customercare']);

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort('Method Not Allowed.', 405);
}

$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
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

$uploadDir = UPLOADS_PATH . 'invoice/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$fileName = time() . '_' . basename($file['name']);
$target   = $uploadDir . $fileName;

// Log the target path so you can verify exactly where the PDF lands
error_log("Invoice file will be stored at: " . $target);

if (!move_uploaded_file($file['tmp_name'], $target)) {
    abort('Failed to move uploaded file.', 500);
}

try {
    $model = new InvoiceModel();                           // thêm
    $model->attachFile($invoiceId, $fileName);             // thay cho prepare/execute trực tiếp

    // redirect back to review page on success
    header('Location: ' . $bootstrap['base_url'] . 'public/pages/invoice/invoice_review.php');
    exit;
} catch (PDOException $e) {
    error_log('Error in process_invoice_send: ' . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    abort('Database error.', 500);
}
