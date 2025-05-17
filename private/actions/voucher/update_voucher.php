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
    'start_date' => $_POST['start_date'] ?? '',
    'end_date' => $_POST['end_date'] ?? '',
    'is_active' => isset($_POST['is_active']) ? 1 : 0,
];
if ($data['code'] === '' || $data['voucher_type'] === '' || $data['discount_value'] === '' || $data['start_date'] === '' || $data['end_date'] === '') {
    api_error('Missing required fields', 400);
}

$model = new VoucherModel();
try {
    $updated = $model->update($id, $data);
    if (!$updated) {
        api_error('Failed to update voucher', 500);
    }
    api_success(['updated' => true]);
} catch (Exception $e) {
    error_log('Error in update_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
