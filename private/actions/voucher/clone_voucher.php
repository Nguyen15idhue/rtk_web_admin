<?php
// filepath: private/actions/voucher/clone_voucher.php

require_once BASE_PATH . '/classes/VoucherModel.php';
require_once BASE_PATH . '/classes/Database.php'; 
require_once BASE_PATH . '/classes/Auth.php'; 
require_once BASE_PATH . '/classes/ActivityLogModel.php';

// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::ensureAuthorized('voucher_management_edit'); // Ensure user is authorized
    $voucher_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if (!$voucher_id) {
        api_error('ID voucher không hợp lệ', 400);
        return;
    }
    
    clone_voucher($voucher_id); // Gọi hàm, hàm sẽ tự xử lý output JSON và exit
    // Không cần echo json_encode($result) nữa
    return; // return ở đây để đảm bảo script kết thúc sau khi clone_voucher đã chạy (mặc dù clone_voucher cũng có thể đã exit)
}

function clone_voucher($voucher_id) {
    try {
        $db = Database::getInstance()->getConnection(); 
        $voucherModel = new VoucherModel(); 
        
        // Get original voucher details using getOne method
        $original = $voucherModel->getOne($voucher_id);
        if (!$original) {
            // Trả về JSON error trực tiếp thay vì array
            api_error('Voucher gốc không tồn tại để nhân bản.', 404);
            return; // Dừng thực thi hàm
        }
        
        // Generate new unique code
        $new_code = generate_unique_voucher_code();
        
        // Clone data with modifications
        $clone_data = [
            'code' => $new_code,
            'description' => ($original['description'] ? $original['description'] . ' (Bản sao)' : 'Bản sao của ' . $original['code']),
            'voucher_type' => $original['voucher_type'],
            'discount_value' => $original['discount_value'],
            'max_discount' => $original['max_discount'],
            'min_order_value' => $original['min_order_value'],
            'quantity' => $original['quantity'],
            'max_sa' => $original['max_sa'],
            'location_id' => $original['location_id'],
            'package_id' => $original['package_id'],
            'start_date' => date('Y-m-d H:i:s'), // Start from today, full datetime
            'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')), // 30 days from now, full datetime
            'is_active' => 0, // Start as inactive
        ];
        
        // Create cloned voucher using create method
        $new_voucher_id = $voucherModel->create($clone_data);
        
        // $voucherModel->create() sẽ ném InvalidArgumentException nếu dữ liệu clone không hợp lệ
        // hoặc trả về false/ID nếu có lỗi DB hoặc thành công.
        if (!$new_voucher_id) {
            // Trả về JSON error trực tiếp
            api_error('Không thể tạo voucher nhân bản do lỗi lưu trữ.', 500);
            return; // Dừng thực thi hàm
        }

        $adminId = $_SESSION['admin_id'] ?? null;
        $logData = [
            ':user_id'        => $adminId,
            ':action'         => 'voucher_cloned',
            ':entity_type'    => 'voucher',
            ':entity_id'      => $new_voucher_id,
            ':old_values'     => json_encode(['original_voucher_id' => $voucher_id, 'original_code' => $original['code']]),
            ':new_values'     => json_encode($clone_data),
            ':notify_content' => null // Removed notification content
        ];
        ActivityLogModel::addLog($db, $logData);

        // Trả về JSON success trực tiếp
        api_success([
            'message' => "Đã nhân bản voucher thành công với mã: {$new_code}",
            'voucher_id' => $new_voucher_id,
            'voucher_code' => $new_code
        ]);
        return; // Dừng thực thi hàm
    } catch (InvalidArgumentException $e) {
        // Lỗi validation từ $voucherModel->create()
        error_log("Clone voucher validation error: " . $e->getMessage());
        api_error('Lỗi dữ liệu khi nhân bản voucher: ' . $e->getMessage(), 400);
        return; // Dừng thực thi hàm
    } catch (Exception $e) {
        error_log("Clone voucher system error: " . $e->getMessage());
        api_error('Lỗi hệ thống khi nhân bản voucher: ' . $e->getMessage(), 500);
        return; // Dừng thực thi hàm
    }
}

function generate_unique_voucher_code($prefix = 'COPY', $length = 8) {
    $db = Database::getInstance()->getConnection(); // Ensure DB instance is available
    
    do {
        $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        $code = $prefix . $random;
        
        // Check if code exists
        $stmt = $db->prepare("SELECT id FROM voucher WHERE code = ?");
        $stmt->execute([$code]);
        $exists = $stmt->fetch();
        
    } while ($exists);
    
    return $code;
}
?>
