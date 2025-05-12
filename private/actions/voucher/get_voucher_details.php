<?php
// filepath: private/actions/voucher/get_voucher_details.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']);
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';

// Get voucher details by ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    api_error('Invalid voucher ID', 400);
}
$model = new VoucherModel();
$voucher = $model->getOne($id);
if (!$voucher) {
    api_error('Voucher not found', 404);
}
api_success($voucher);
