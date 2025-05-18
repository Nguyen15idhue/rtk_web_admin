<?php
// filepath: private/actions/voucher/delete_voucher.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('voucher_management_edit');
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';

// Validate input
$id = isset($_POST['id']) ? (int)
    $_POST['id'] : 0;
if ($id <= 0) {
    api_error('ID Voucher không hợp lệ', 400);
}
$model = new VoucherModel();
try {
    $deleted = $model->delete($id);
    if (!$deleted) {
        api_error('Lỗi khi xóa voucher', 500);
    }
    api_success(null, 'Xóa voucher thành công');
} catch (Exception $e) {
    error_log('Error in delete_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
