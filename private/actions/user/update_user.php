<?php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin', 'customercare']); 

$config = require_once __DIR__ . '/../../includes/page_bootstrap.php';
$db = $config['db'];
header('Content-Type: application/json');

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

    if (!$db) {
        error_log("DB connection failed in update_user");
        abort('Database connection failed.', 500);
    }

    try {
        $db->beginTransaction();

        $check_sql = "SELECT id FROM user WHERE id != :id AND (email = :email";
        $params = [':id' => $user_id, ':email' => $email];
        if (!empty($phone)) {
            $check_sql .= " OR phone = :phone";
            $params[':phone'] = $phone;
        }
        $check_sql .= ")";

        $stmt_check = $db->prepare($check_sql);
        $stmt_check->execute($params);

        if ($stmt_check->fetch()) {
            $db->rollBack();
            abort('Email hoặc số điện thoại đã tồn tại cho người dùng khác.', 409);
        }

        $sql = "UPDATE user SET
                    username = :username,
                    email = :email,
                    phone = :phone,
                    is_company = :is_company,
                    company_name = :company_name,
                    tax_code = :tax_code,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt_update = $db->prepare($sql);
        $stmt_update->bindParam(':username', $username);
        $stmt_update->bindParam(':email', $email);
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
            $db->commit();
            api_success(null, 'Cập nhật thông tin người dùng thành công.');
        } else {
            $db->rollBack();
            abort('Không thể cập nhật thông tin người dùng.', 500);
        }
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Transaction PDOException in update_user: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
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