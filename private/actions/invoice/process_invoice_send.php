<?php
declare(strict_types=1);

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed.');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';

$invoiceId = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;
if ($invoiceId <= 0 || empty($_FILES['invoice_file'])) {
    die('Invalid request.');
}

$file = $_FILES['invoice_file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    die('Upload error.');
}
if (mime_content_type($file['tmp_name']) !== 'application/pdf') {
    die('Invalid file type.');
}

$uploadDir = __DIR__ . '/../../../public/uploads/invoice/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$fileName = time() . '_' . basename($file['name']);
$target = $uploadDir . $fileName;
if (!move_uploaded_file($file['tmp_name'], $target)) {
    die('Failed to move uploaded file.');
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare(
      "UPDATE invoice 
         SET status = 'approved', invoice_file = :file 
       WHERE id = :id"
    );
    $stmt->execute([':file' => $fileName, ':id' => $invoiceId]);
    header('Location: ../../../public/pages/invoice_review.php');
    exit;
} catch (PDOException $e) {
    error_log('Error in process_invoice_send: ' . $e->getMessage());
    die('Database error.');
}
