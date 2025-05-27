<?php
// filepath: private/actions/voucher/toggle_voucher_status.php
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
$action = $_POST['action'] ?? '';
if ($id <= 0 || !in_array($action, ['enable', 'disable'], true)) {
    api_error('Invalid request', 400);
}

$model = new VoucherModel();
try {
    $disable = $action === 'disable';
    
    // Get voucher details before status change for logging
    $voucher = $model->getOne($id);
    if (!$voucher) {
        api_error('Voucher not found', 404);
    }
    
    $ok = $model->toggleStatus($id, $disable);
    if (!$ok) {
        api_error('Failed to toggle status', 500);
    }
    
    // Log the status change
    try {
        $db = Database::getInstance()->getConnection();
        $adminId = $_SESSION['admin_id'] ?? null;
        
        $old_status = $voucher['is_active'] ? 'active' : 'inactive';
        $new_status = $disable ? 'inactive' : 'active';
        
        ActivityLogModel::addLog(
            $db,
            [
                ':user_id'        => $adminId,
                ':action'         => 'voucher_status_changed',
                ':entity_type'    => 'voucher',
                ':entity_id'      => $id,
                ':old_values'     => json_encode(['status' => $old_status]),
                ':new_values'     => json_encode(['status' => $new_status]),
                ':notify_content' => "Voucher '{$voucher['code']}' status changed from {$old_status} to {$new_status}"
            ]
        );
    } catch (Exception $logError) {
        error_log('Failed to log voucher status change: ' . $logError->getMessage());
    }
    
    api_success(['status' => $disable ? 'inactive' : 'active']);
} catch (Exception $e) {
    error_log('Error in toggle_voucher_status: ' . $e->getMessage());
    api_error('Server error', 500);
}
