<?php
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('user_management_edit'); 
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

    $user_id      = filter_input(INPUT_POST, 'user_id',      FILTER_VALIDATE_INT);
    
    $username     = isset($_POST['username']) ? trim((string)$_POST['username']) : ''; 
    $email        = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone        = isset($_POST['phone']) ? trim((string)$_POST['phone']) : null; 

    $is_company   = (isset($_POST['is_company']) && $_POST['is_company'] === '1') ? 1 : 0;

    $company_name_input = isset($_POST['company_name']) ? trim((string)$_POST['company_name']) : '';
    $tax_code_input     = isset($_POST['tax_code']) ? trim((string)$_POST['tax_code']) : '';
    $company_address_input = isset($_POST['company_address']) ? trim((string)$_POST['company_address']) : '';

    if ($is_company) {
        $company_name = $company_name_input;
        $tax_code     = $tax_code_input;
        $company_address = $company_address_input;
    } else {
        $company_name = null;
        $tax_code     = null;
        $company_address = null;
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
    if ($is_company && empty($company_name)) {
        abort('Tên công ty không được để trống nếu chọn loại tài khoản là công ty.', 400);
    }

    try {
        $userModel->updateWithDuplicateCheck($user_id, [
            'username'     => $username,
            'email'        => $email,
            'phone'        => $phone,
            'is_company'   => $is_company,
            'company_name' => $company_name,
            'tax_code'     => $tax_code,
            'company_address' => $company_address
        ]);
        api_success(null, 'Cập nhật thông tin người dùng thành công.');
    } catch (Exception $e) {
        error_log("DEBUG update_user Exception: user_id={$user_id} | username={$username} | email={$email} | phone={$phone} | is_company={$is_company} | company_name={$company_name} | tax_code={$tax_code} | company_address={$company_address} | msg=" . $e->getMessage());
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