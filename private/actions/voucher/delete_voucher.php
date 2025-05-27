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
require_once __DIR__ . '/../../classes/ActivityLogModel.php';
require_once __DIR__ . '/../../classes/Database.php';

// Validate input
$id = isset($_POST['id']) ? (int)
    $_POST['id'] : 0;
if ($id <= 0) {
    api_error('ID Voucher không hợp lệ', 400);
}
$model = new VoucherModel();
try {
    // Get voucher details before deletion for logging
    $voucher = $model->getOne($id);
    if (!$voucher) {
        api_error('Voucher not found', 404);
    }
    
    $deleted = $model->delete($id);
    if (!$deleted) {
        api_error('Lỗi khi xóa voucher', 500);
    }
    
    // Log the voucher deletion
    try {
        $db = Database::getInstance()->getConnection();
        $adminId = $_SESSION['admin_id'] ?? null;
        
        ActivityLogModel::addLog(
            $db,
            [
                ':user_id'        => $adminId,
                ':action'         => 'voucher_deleted',
                ':entity_type'    => 'voucher',
                ':entity_id'      => $id,
                ':old_values'     => json_encode([
                    'voucher_code'    => $voucher['code'],
                    'voucher_type'    => $voucher['voucher_type'],
                    'discount_value'  => $voucher['discount_value'],
                    'start_date'      => $voucher['start_date'],
                    'end_date'        => $voucher['end_date'],
                    'is_active'       => $voucher['is_active'],
                    'location_id'     => $voucher['location_id'],
                    'package_id'      => $voucher['package_id']
                ]),
                ':new_values'     => null,
                ':notify_content' => "Voucher '{$voucher['code']}' has been deleted"
            ]
        );
    } catch (Exception $logError) {
        error_log('Failed to log voucher deletion: ' . $logError->getMessage());
    }
    
    api_success(null, 'Xóa voucher thành công');
} catch (Exception $e) {
    error_log('Error in delete_voucher: ' . $e->getMessage());
    api_error('Server error', 500);
}
