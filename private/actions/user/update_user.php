<?php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']); 

$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db = $config['db'];
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/UserModel.php';
$userModel = new UserModel();

try {
    if (!isset($_SESSION['admin_id']) || !in_array($_SESSION['admin_role'] ?? '', ['admin', 'admin'])) {
        error_log("Unauthorized access to update_user");
        abort('Unauthorized access.', 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
        abort('Invalid request.', 400);
    }

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tax_code     = filter_input(INPUT_POST, 'tax_code',     FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // Nếu cả hai rỗng thì không phải công ty
    $is_company = (!empty($company_name) || !empty($tax_code)) ? 1 : 0;
    if (empty($company_name) && empty($tax_code)) {
        $company_name = null;
        $tax_code     = null;
    }

    if (!$user_id) {
        abort('ID người dùng không hợp lệ.', 400);
    }
    if (empty($username)) {
        abort('Tên đăng nhập không được để trống.', 400);
    }
    if (!$email) {
        abort('Email không hợp lệ.', 400);
    }

    try {
        $userModel->updateWithDuplicateCheck($user_id, [
            'username'     => $username,
            'email'        => $email,
            'phone'        => $phone,
            'is_company'   => $is_company,
            'company_name' => $company_name,
            'tax_code'     => $tax_code
        ]);
        api_success(null, 'Cập nhật thông tin người dùng thành công.');
    } catch (Exception $e) {
        $msg = $e->getMessage();
        abort($msg, strpos($msg, 'tồn tại')!==false ? 409 : 500);
    }
} catch (PDOException $e) {
    error_log("DB Error in update_user: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Lỗi cơ sở dữ liệu. Vui lòng thử lại.', 500);
} catch (Exception $e) {
    error_log("General Error in update_user: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    abort('Đã xảy ra lỗi không mong muốn.', 500);
} finally {
    if (isset($db)) {
        $db = null;
    }
}
?>