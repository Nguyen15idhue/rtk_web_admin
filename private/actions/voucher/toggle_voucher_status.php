<?php
// filepath: private/actions/voucher/toggle_voucher_status.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']);
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = $_POST['action'] ?? '';
if ($id <= 0 || !in_array($action, ['enable', 'disable'], true)) {
    api_error('Invalid request', 400);
}

$model = new VoucherModel();
try {
    $disable = $action === 'disable';
    $ok = $model->toggleStatus($id, $disable);
    if (!$ok) {
        api_error('Failed to toggle status', 500);
    }
    api_success(['status' => $disable ? 'inactive' : 'active']);
} catch (Exception $e) {
    error_log('Error in toggle_voucher_status: ' . $e->getMessage());
    api_error('Server error', 500);
}
