<?php
// filepath: private\actions\account\delete_account.php

// Sử dụng bootstrap chung thay vì include thủ công
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';
$db     = $config['db'];
$base   = $config['base_path'];

// Đảm bảo đóng kết nối PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

// Sử dụng lớp Auth để xác thực và phân quyền
Auth::ensureAuthorized('account_management_edit');

// Lấy dữ liệu đầu vào
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!$input || !isset($input['id'])) {
    abort('Dữ liệu đầu vào không hợp lệ. ID tài khoản là bắt buộc.', 400);
}

$accountId = filter_var($input['id'], FILTER_SANITIZE_SPECIAL_CHARS);

if (!$db) {
    error_log("Database connection failed in delete_account.php");
    abort('Lỗi kết nối cơ sở dữ liệu.', 500);
}

$accountModel = new AccountModel($db);

// --- Giao dịch cơ sở dữ liệu (Tùy chọn nhưng được khuyến nghị) ---
$db->beginTransaction();

try {
    // Thực hiện xóa vĩnh viễn khỏi cơ sở dữ liệu
    $success = $accountModel->hardDeleteAccount($accountId);

    if ($success) {
        // gọi hàm API tập trung
        $apiResult = deleteRtkAccount([$accountId]);
        if (!$apiResult['success']) {
            throw new Exception('Xóa tài khoản qua API bên ngoài thất bại: ' . $apiResult['error']);
        }

        $db->commit();
        api_success(null, 'Tài khoản đã được đánh dấu xóa thành công.');
    } else {
        $db->rollBack();
        api_error('Không thể đánh dấu tài khoản để xóa trong cơ sở dữ liệu.', 500);
    }
} catch (Exception $e) {
    $db->rollBack();
    error_log("Error in delete_account.php: " . $e->getMessage());
    abort('Đã xảy ra lỗi: ' . $e->getMessage(), 500);
}
