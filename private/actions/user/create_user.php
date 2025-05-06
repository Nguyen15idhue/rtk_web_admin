<?php
$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$conn     = $config['db'];
if (!isset($_SESSION['admin_id'])) {
    error_log("Unauthorized access attempt to process_user_create.php");
    api_error('Unauthorized access.', 403);
}

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

try {
    $stmt_check = $conn->prepare("SELECT id FROM user WHERE email = :email OR (phone IS NOT NULL AND phone = :phone)");
    $stmt_check->bindParam(':email', $email);
    $stmt_check->bindParam(':phone', $phone);
    $stmt_check->execute();
    if ($stmt_check->fetch()) {
        api_error('Email hoặc số điện thoại đã tồn tại.', 409);
    }
} catch (PDOException $e) {
    error_log("Database Error (Check Existing User) in process_user_create.php: " . $e->getMessage() . " | Email: " . $email . " | Phone: " . $phone);
    api_error('Lỗi cơ sở dữ liệu khi kiểm tra thông tin người dùng. Vui lòng thử lại.', 500);
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO user (username, email, password, phone, is_company, company_name, tax_code, created_at)
            VALUES (:username, :email, :password, :phone, :is_company, :company_name, :tax_code, NOW())";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':is_company', $is_company, PDO::PARAM_INT);
    $stmt->bindParam(':company_name', $company_name);
    $stmt->bindParam(':tax_code', $tax_code);

    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        try {
            $settings_sql = "INSERT INTO user_settings (user_id, created_at) VALUES (:user_id, NOW())";
            $settings_stmt = $conn->prepare($settings_sql);
            $settings_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $settings_stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error (Create Settings for user_id: " . $user_id . ") in process_user_create.php: " . $e->getMessage());
        }

        api_success(['id' => $user_id], 'Thêm người dùng thành công.');
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Database Error (Insert User Failed Execution) in process_user_create.php: SQLSTATE[" . $errorInfo[0] . "] [" . $errorInfo[1] . "] " . $errorInfo[2]);
        api_error('Không thể thêm người dùng vào cơ sở dữ liệu (lỗi thực thi).', 500);
    }
} catch (PDOException $e) {
    error_log("Database Error (Insert User) in process_user_create.php: " . $e->getMessage());
    api_error('Đã xảy ra lỗi cơ sở dữ liệu khi thêm người dùng. Vui lòng thử lại.', 500);
} catch (Exception $e) {
    error_log("Unexpected Error in process_user_create.php during user insertion: " . $e->getMessage());
    api_error('Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.', 500);
}
?>