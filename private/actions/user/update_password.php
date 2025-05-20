<?php
// filepath: private/actions/user/update_password.php
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';
Auth::ensureAuthorized('user_management_edit');
$db = $config['db'];
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
}

// Read JSON input or form data
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

$user_id = filter_var($input['user_id'] ?? null, FILTER_VALIDATE_INT);
$password = $input['password'] ?? '';
$confirm = $input['confirm_password'] ?? '';

if (!$user_id) {
    api_error('ID người dùng không hợp lệ.', 400);
}
if (empty($password) || empty($confirm)) {
    api_error('Mật khẩu và xác nhận mật khẩu là bắt buộc.', 400);
}
if ($password !== $confirm) {
    api_error('Mật khẩu xác nhận không khớp.', 400);
}
require_once __DIR__ . '/../../classes/UserModel.php';
$userModel = new UserModel();
try {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $ok = $userModel->updatePassword($user_id, $hashed);
    if ($ok) {
        api_success(null, 'Đổi mật khẩu thành công.');
    } else {
        api_error('Không thể đổi mật khẩu.', 500);
    }
} catch (Exception $e) {
    error_log('Error in update_password: ' . $e->getMessage());
    api_error('Đã xảy ra lỗi khi đổi mật khẩu.', 500);
}

