<?php
// filepath: private/actions/voucher/delete_voucher.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('voucher_management');
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';

// Validate input
$id = isset($_POST['id']) ? (int)
    $_POST['id'] : 0;
if ($id <= 0) {
    api_error('Invalid voucher ID', 400);
}
$model = new VoucherModel();
try {
    $deleted = $model->delete($id);
    if (!$deleted) {
        api_error('Failed to delete voucher', 500);
    }
    api_success(null, 'Voucher deleted');
} catch (Exception $e) {
    error_log('Error in delete_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
