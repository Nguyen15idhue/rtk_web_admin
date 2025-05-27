<?php
// filepath: private/actions/voucher/clone_voucher.php

require_once BASE_PATH . '/classes/VoucherModel.php';
require_once BASE_PATH . '/utils/functions.php';

// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucher_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if (!$voucher_id) {
        api_error('ID voucher không hợp lệ', 400);
        return;
    }
    
    $result = clone_voucher($voucher_id);
    echo json_encode($result);
    return;
}

function clone_voucher($voucher_id) {
    global $db;
    
    try {
        $voucherModel = new VoucherModel();
        
        // Get original voucher details using getOne method
        $original = $voucherModel->getOne($voucher_id);
        if (!$original) {
            return [
                'success' => false, 
                'message' => 'Voucher không tồn tại'
            ];
        }
        
        // Generate new unique code
        $new_code = generate_unique_voucher_code();
        
        // Clone data with modifications
        $clone_data = [
            'code' => $new_code,
            'description' => $original['description'] . ' (Bản sao)',
            'voucher_type' => $original['voucher_type'],
            'discount_value' => $original['discount_value'],
            'max_discount' => $original['max_discount'],
            'min_order_value' => $original['min_order_value'],
            'quantity' => $original['quantity'],
            'max_sa' => $original['max_sa'],
            'location_id' => $original['location_id'],
            'package_id' => $original['package_id'],
            'start_date' => date('Y-m-d'), // Start from today
            'end_date' => date('Y-m-d', strtotime('+30 days')), // 30 days from now
            'is_active' => 0, // Start as inactive
            'created_by' => $_SESSION['admin_id']
        ];
        
        // Create cloned voucher using create method
        $new_voucher_id = $voucherModel->create($clone_data);
        
        if ($new_voucher_id) {
            return [
                'success' => true,
                'message' => "Đã nhân bản voucher thành công với mã: {$new_code}",
                'voucher_id' => $new_voucher_id,
                'voucher_code' => $new_code
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể tạo voucher nhân bản'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Clone voucher error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Lỗi hệ thống: ' . $e->getMessage()
        ];
    }
}

function generate_unique_voucher_code($prefix = 'COPY', $length = 8) {
    $db = Database::getInstance()->getConnection();
    
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
