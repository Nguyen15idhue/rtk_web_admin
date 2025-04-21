<?php
session_start();

// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

header('Content-Type: application/json');

// Check if admin is logged in (optional but recommended for security)
if (!isset($_SESSION['admin_id'])) {
    error_log("Unauthorized access attempt to process_user_create.php"); // Log attempt
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Correct the paths by going up two levels instead of three
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';

$conn = null; // Initialize $conn to null
try {
    $db = new Database();
    $conn = $db->getConnection();
    // Optional: Log successful connection for debugging
    // error_log("Database connection successful in process_user_create.php");
} catch (PDOException $e) {
    error_log("FATAL: Database Connection Error in process_user_create.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối cơ sở dữ liệu. Không thể xử lý yêu cầu.']);
    exit;
} catch (Exception $e) { // Catch other potential errors during DB class instantiation
    error_log("FATAL: Error initializing Database class in process_user_create.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi khởi tạo hệ thống cơ sở dữ liệu.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get JSON data from the request body
$rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        $input = $_POST;
    }

// --- Input Validation: Check if JSON decoding was successful and is an array ---
if (!is_array($input)) {
    $raw_input = file_get_contents('php://input');
    error_log("Invalid JSON input received in process_user_create.php. Raw input: " . $raw_input);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu gửi lên không hợp lệ (không phải JSON hoặc sai định dạng).']);
    exit;
}

// --- Basic Validation ---
if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Tên người dùng, email và mật khẩu là bắt buộc.']);
    exit;
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Địa chỉ email không hợp lệ.']);
    exit;
}

// --- Prepare Data ---
$username = trim($input['username']);
$email = trim($input['email']);
$password = $input['password']; // Will be hashed
$phone = isset($input['phone']) ? trim($input['phone']) : null;
$is_company = isset($input['is_company']) && $input['is_company'] == 1 ? 1 : 0;
$company_name = ($is_company && isset($input['company_name'])) ? trim($input['company_name']) : null;
$tax_code = ($is_company && isset($input['tax_code'])) ? trim($input['tax_code']) : null;

// Additional validation for company fields if is_company is true
if ($is_company && (empty($company_name) || empty($tax_code))) {
    echo json_encode(['success' => false, 'message' => 'Tên công ty và mã số thuế là bắt buộc nếu chọn là công ty.']);
    exit;
}

// --- Check for Existing Email/Phone ---
try {
    $stmt_check = $conn->prepare("SELECT id FROM user WHERE email = :email OR (phone IS NOT NULL AND phone = :phone)");
    $stmt_check->bindParam(':email', $email);
    $stmt_check->bindParam(':phone', $phone); // Ensure phone is bound even if null
    $stmt_check->execute();
    if ($stmt_check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email hoặc số điện thoại đã tồn tại.']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Database Error (Check Existing User) in process_user_create.php: " . $e->getMessage() . " | Email: " . $email . " | Phone: " . $phone);
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu khi kiểm tra thông tin người dùng. Vui lòng thử lại.']);
    exit;
}

// --- Hash Password ---
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// --- Insert User ---
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
        // --- Create default user settings (nested try-catch) ---
        try {
            $settings_sql = "INSERT INTO user_settings (user_id, created_at) VALUES (:user_id, NOW())";
            $settings_stmt = $conn->prepare($settings_sql);
            $settings_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $settings_stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error (Create Settings for user_id: " . $user_id . ") in process_user_create.php: " . $e->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Thêm người dùng thành công.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Database Error (Insert User Failed Execution) in process_user_create.php: SQLSTATE[" . $errorInfo[0] . "] [" . $errorInfo[1] . "] " . $errorInfo[2]);
        echo json_encode(['success' => false, 'message' => 'Không thể thêm người dùng vào cơ sở dữ liệu (lỗi thực thi).']);
    }
} catch (PDOException $e) {
    error_log("Database Error (Insert User) in process_user_create.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi cơ sở dữ liệu khi thêm người dùng. Vui lòng thử lại.']);
    exit;
} catch (Exception $e) {
    error_log("Unexpected Error in process_user_create.php during user insertion: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.']);
    exit;
}

?>