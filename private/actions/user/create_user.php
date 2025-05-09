<?php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']); 

$config = require_once __DIR__ . '/../../core/page_bootstrap.php';
$db     = $config['db'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_error('Invalid request method.', 405);
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

if (!is_array($input)) {
    error_log("Invalid JSON input received in process_user_create.php. Raw input: " . $rawInput);
    api_error('Dữ liệu gửi lên không hợp lệ (không phải JSON hoặc sai định dạng).', 400);
}

if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
    api_error('Tên người dùng, email và mật khẩu là bắt buộc.', 400);
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    api_error('Địa chỉ email không hợp lệ.', 400);
}

$username = trim($input['username']);
$email = trim($input['email']);
$password = $input['password'];
$phone = isset($input['phone']) ? trim($input['phone']) : null;
$is_company = isset($input['is_company']) && $input['is_company'] == 1 ? 1 : 0;
$company_name = ($is_company && isset($input['company_name'])) ? trim($input['company_name']) : null;
$tax_code = ($is_company && isset($input['tax_code'])) ? trim($input['tax_code']) : null;

if ($is_company && (empty($company_name) || empty($tax_code))) {
    api_error('Tên công ty và mã số thuế là bắt buộc nếu chọn là công ty.', 400);
}

require_once __DIR__ . '/../../classes/UserModel.php';
$userModel = new UserModel();

try {
    $userId = $userModel->createWithSettings([
        'username'     => $username,
        'email'        => $email,
        'password'     => password_hash($password, PASSWORD_DEFAULT),
        'phone'        => $phone,
        'is_company'   => $is_company,
        'company_name' => $company_name,
        'tax_code'     => $tax_code
    ]);
    api_success(['id' => $userId], 'Thêm người dùng thành công.');
} catch (Exception $e) {
    error_log("Create user error: " . $e->getMessage());
    api_error($e->getMessage(), 500);
}
?>