<?php
// filepath: private/actions/voucher/update_voucher.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('voucher_management_edit');
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';
require_once __DIR__ . '/../../classes/ActivityLogModel.php';
require_once __DIR__ . '/../../classes/Database.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    api_error('Invalid voucher ID', 400);
}

// Validate input
$data = [
    'code' => trim($_POST['code'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'voucher_type' => $_POST['voucher_type'] ?? '',
    'discount_value' => $_POST['discount_value'] ?? '',
    'max_discount' => $_POST['max_discount'] ?? null,
    'min_order_value' => $_POST['min_order_value'] ?? null,
    'quantity' => $_POST['quantity'] ?? null,
    'limit_usage' => $_POST['limit_usage'] ?? null,
    'max_sa' => $_POST['max_sa'] ?? null,
    'location_id' => $_POST['location_id'] ?? null,
    'package_id' => $_POST['package_id'] ?? null,
    'start_date' => $_POST['start_date'] ?? '',
    'end_date' => $_POST['end_date'] ?? '',
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
];
if ($data['code'] === '' || $data['voucher_type'] === '' || $data['discount_value'] === '' || $data['start_date'] === '' || $data['end_date'] === '') {
    api_error('Missing required fields', 400);
}

// Convert empty string optional numeric fields to null, so DB receives proper NULL
foreach (['max_discount', 'min_order_value', 'quantity', 'limit_usage', 'max_sa', 'location_id', 'package_id'] as $field) {
    if (isset($data[$field]) && $data[$field] === '') {
        $data[$field] = null;
    }
}

$model = new VoucherModel();
try {
    // Get old voucher data for logging
    $oldVoucher = $model->getOne($id);
    
    $updated = $model->update($id, $data);
    if (!$updated) {
        api_error('Failed to update voucher', 500);
    }
    
    // Log voucher update activity
    $db = Database::getInstance()->getConnection();
    $adminId = $_SESSION['admin_id'] ?? null;
    
    // Prepare old and new values for comparison
    $oldValues = $oldVoucher ? [
        'voucher_code'    => $oldVoucher['code'],
        'voucher_type'    => $oldVoucher['voucher_type'],
        'discount_value'  => $oldVoucher['discount_value'],
        'start_date'      => $oldVoucher['start_date'],
        'end_date'        => $oldVoucher['end_date'],
        'is_active'       => $oldVoucher['is_active'],
        'location_id'     => $oldVoucher['location_id'],
        'package_id'      => $oldVoucher['package_id']
    ] : null;
    
    $newValues = [
        'voucher_code'    => $data['code'],
        'voucher_type'    => $data['voucher_type'],
        'discount_value'  => $data['discount_value'],
        'start_date'      => $data['start_date'],
        'end_date'        => $data['end_date'],
        'is_active'       => $data['is_active'],
        'location_id'     => $data['location_id'],
        'package_id'      => $data['package_id']
    ];
    
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'        => $adminId, // Admin who updated the voucher
            ':action'         => 'voucher_updated',
            ':entity_type'    => 'voucher',
            ':entity_id'      => $id,
            ':old_values'     => json_encode($oldValues),
            ':new_values'     => json_encode($newValues),
            ':notify_content' => "Voucher '{$data['code']}' đã được cập nhật."
        ]
    );
    
    api_success(['updated' => true]);
} catch (Exception $e) {
    error_log('Error in update_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
