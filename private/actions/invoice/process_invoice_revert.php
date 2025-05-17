<?php
require_once __DIR__ . '/../../core/page_bootstrap.php';
header('Content-Type: application/json');
Auth::ensureAuthorized('invoice_review_edit');

require_once __DIR__ . '/../../classes/InvoiceModel.php'; // Corrected path
require_once __DIR__ . '/../../classes/ActivityLogModel.php'; // Corrected path

$input = json_decode(file_get_contents('php://input'), true);
$invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id'] : 0;

if (!$invoiceId) {
    echo json_encode(['success' => false, 'message' => 'Invoice ID không hợp lệ']);
    exit;
}

$model = new InvoiceModel(); // Instantiate InvoiceModel
$invoice = $model->getOne($invoiceId); // Get current invoice details

if (!$invoice) {
    echo json_encode(['success' => false, 'message' => 'Invoice không tìm thấy']);
    exit;
}

// Fetch customer ID via model
$customerId = $model->getCustomerId($invoiceId);

$oldStatus = $invoice['status']; // Capture old status

$stmt = $db->prepare("UPDATE invoice SET status = 'pending', rejected_reason = NULL, invoice_file = NULL WHERE id = ?");
if ($stmt->execute([$invoiceId])) {
    // Log activity
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'      => $customerId, // Use model-provided customerId
            ':action'       => 'invoice_reverted',
            ':entity_type'  => 'invoice',
            ':entity_id'    => $invoiceId,
            ':old_values'   => json_encode(['status' => $oldStatus]),
            ':new_values'   => json_encode(['status' => 'pending', 'customer_id' => $customerId]),
            ':notify_content'  => "Hóa đơn #{$invoiceId} đang gặp vấn đề và cần chỉnh sửa lại." // Ensure description is passed
        ]
    );
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
}
