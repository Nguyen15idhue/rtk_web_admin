<?php
// filepath: private/actions/voucher/create_voucher.php
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
    
    // Log voucher creation activity
    $db = Database::getInstance()->getConnection();
    $adminId = $_SESSION['admin_id'] ?? null;
    
    ActivityLogModel::addLog(
        $db,
        [
            ':user_id'        => $adminId, // Admin who created the voucher
            ':action'         => 'voucher_created',
            ':entity_type'    => 'voucher',
            ':entity_id'      => $id,
            ':old_values'     => null,
            ':new_values'     => json_encode([
                'voucher_code'    => $data['code'],
                'voucher_type'    => $data['voucher_type'],
                'discount_value'  => $data['discount_value'],
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
                'is_active'       => $data['is_active'],
                'location_id'     => $data['location_id'],
                'package_id'      => $data['package_id']
            ]),
            ':notify_content' => "Voucher mới '{$data['code']}' đã được tạo với giá trị " . 
                               ($data['voucher_type'] === 'percentage_discount' ? 
                                "{$data['discount_value']}%" : 
                                number_format($data['discount_value'], 0, ',', '.') . ' VNĐ') . "."
        ]
    );
    
    api_success(['id' => $id]);
} catch (Exception $e) {
    // Log and include exception message in response for debugging
    error_log('Error in create_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
