<?php
declare(strict_types=1);
header('Content-Type: application/json');

$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
$db        = $bootstrap['db'];

$input = json_decode(file_get_contents('php://input'), true);
$invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id'] : 0;
$reason = isset($input['reason']) ? trim($input['reason']) : '';

if ($invoiceId <= 0 || $reason === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid invoice ID or reason.']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE invoice SET status = 'rejected', rejected_reason = :reason WHERE id = :id");
    $stmt->execute([':reason' => $reason, ':id' => $invoiceId]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Error in process_invoice_reject: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
