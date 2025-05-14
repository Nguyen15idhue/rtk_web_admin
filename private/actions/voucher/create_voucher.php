<?php
// filepath: private/actions/voucher/create_voucher.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('voucher_management');
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';

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

// Convert empty string optional numeric fields to null, so DB receives proper NULL
foreach (['max_discount', 'min_order_value', 'quantity', 'limit_usage'] as $field) {
    if (isset($data[$field]) && $data[$field] === '') {
        $data[$field] = null;
    }
}

// Normalize date values to full datetime if only date is provided
foreach (['start_date', 'end_date'] as $dateField) {
    if (!empty($data[$dateField]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data[$dateField])) {
        $data[$dateField] .= $dateField === 'end_date' ? ' 23:59:59' : ' 00:00:00';
    }
}

$model = new VoucherModel();
try {
    $id = $model->create($data);
    if (!$id) {
        api_error('Failed to create voucher', 500);
    }
    api_success(['id' => $id]);
} catch (Exception $e) {
    // Log and include exception message in response for debugging
    error_log('Error in create_voucher: ' . $e->getMessage());
    error_log('Error in create_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
