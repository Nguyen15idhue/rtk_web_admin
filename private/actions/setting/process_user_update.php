<?php
session_start();
// Correct the paths relative to the current file's directory (private/actions/setting)
require_once __DIR__ . '/../../config/database.php'; // Go up two levels to 'private', then into 'config'
require_once __DIR__ . '/../../classes/Database.php'; // Go up two levels to 'private', then into 'classes'

header('Content-Type: application/json');

// Wrap main logic in try-catch to ensure JSON output even for early errors
try {
    // Authorization Check (Admin only)
    if (!isset($_SESSION['admin_id']) || !in_array($_SESSION['admin_role'] ?? '', ['admin', 'admin'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    // Input Validation
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    // Use FILTER_SANITIZE_FULL_SPECIAL_CHARS for strings to prevent potential XSS if displayed elsewhere
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Basic sanitization
    $is_company = isset($_POST['is_company']) ? 1 : 0;
    $company_name = $is_company ? filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
    $tax_code = $is_company ? filter_input(INPUT_POST, 'tax_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

    // Basic validation checks
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'ID người dùng không hợp lệ.']);
        exit;
    }
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập không được để trống.']);
        exit;
    }
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ.']);
        exit;
    }
    // Add more specific validation for phone, company name, tax code if needed
    // Example: Validate phone format
    // if (!empty($phone) && !preg_match('/^[0-9\s\-\+\(\)]+$/', $phone)) {
    //     echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ.']);
    //     exit;
    // }

    $db = Database::getInstance();;
    $conn = $db->getConnection();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    // Inner try-catch for transaction handling remains
    try {
        $conn->beginTransaction();

        // Check if email or phone already exists for another user (ensure phone is checked only if provided)
        $check_sql = "SELECT id FROM user WHERE id != :id AND (email = :email";
        $params = [':id' => $user_id, ':email' => $email];
        if (!empty($phone)) {
            $check_sql .= " OR phone = :phone";
            $params[':phone'] = $phone;
        }
        $check_sql .= ")";

        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->execute($params); // Execute with parameters array

        if ($stmt_check->fetch()) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Email hoặc số điện thoại đã tồn tại cho người dùng khác.']);
            exit;
        }

        // Update user data
        $sql = "UPDATE user SET
                    username = :username,
                    email = :email,
                    phone = :phone,
                    is_company = :is_company,
                    company_name = :company_name,
                    tax_code = :tax_code,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt_update = $conn->prepare($sql);
        $stmt_update->bindParam(':username', $username);
        $stmt_update->bindParam(':email', $email);
        // Bind phone as null if empty, otherwise bind the value
        if (empty($phone)) {
             $stmt_update->bindValue(':phone', null, PDO::PARAM_NULL);
        } else {
             $stmt_update->bindParam(':phone', $phone);
        }
        $stmt_update->bindParam(':is_company', $is_company, PDO::PARAM_INT);
        $stmt_update->bindParam(':company_name', $company_name);
        $stmt_update->bindParam(':tax_code', $tax_code);
        $stmt_update->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            // TODO: Add activity logging here if needed
            // log_activity($_SESSION['admin_id'], 'update_user', 'user', $user_id, $old_data, $_POST);
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin người dùng thành công.']);
            exit;
        } else {
            $conn->rollBack();
            error_log("Failed to update user ID: " . $user_id . " - Error: " . implode(":", $stmt_update->errorInfo()));
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật thông tin người dùng.']);
            exit;
        }

    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        // Re-throw to be caught by the outer catch block
        throw $e;
    }
    // No need for inner catch (Exception $e) if outer one handles it

} catch (PDOException $e) {
    // Catch PDO exceptions from connection or inner try
    error_log("Database Error in process_user_update.php: " . $e->getMessage());
    http_response_code(500);
    // Ensure JSON output even if headers already sent by potential prior errors
    if (!headers_sent()) {
         header('Content-Type: application/json');
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu. Vui lòng thử lại.']);
    exit;
} catch (Exception $e) {
    // Catch any other general exceptions
    error_log("General Error in process_user_update.php: " . $e->getMessage());
    http_response_code(500);
     // Ensure JSON output even if headers already sent by potential prior errors
    if (!headers_sent()) {
         header('Content-Type: application/json');
    }
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage()]); // Include error message for debugging if needed
    exit;
} finally {
    // Close connection if it was successfully established
    if (isset($conn)) {
        $conn = null;
    }
}

// No code should execute after this point due to exit calls
?>