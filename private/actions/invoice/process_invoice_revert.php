<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
header('Content-Type: application/json');
Auth::ensureAuthorized('invoice_management');

$input = json_decode(file_get_contents('php://input'), true);
$invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id'] : 0;

if (!$invoiceId) {
    echo json_encode(['success' => false, 'message' => 'Invoice ID không hợp lệ']);
    exit;
}

$stmt = $db->prepare("UPDATE invoice SET status = 'pending', rejected_reason = NULL, invoice_file = NULL WHERE id = ?");
if ($stmt->execute([$invoiceId])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
}
