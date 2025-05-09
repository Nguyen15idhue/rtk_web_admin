<?php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url  = $bootstrap['base_url'];
$db = $bootstrap['db'];

register_shutdown_function(function() use (&$db) {
    if (isset($db) && $db instanceof PDO) {
        $db = null;
    }
});

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = trim($_POST['admin_username'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    $login_error = null;

    // --- Brute-force protection ---
    $maxAttempts  = 5;
    $lockoutTime  = 600; // 10 phút
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    $data = $_SESSION['login_attempts'][$admin_username] 
             ?? ['count'=>0, 'first_time'=>time()];
    if ($data['count'] >= $maxAttempts 
        && (time() - $data['first_time']) < $lockoutTime) {
        $remain = ceil(($lockoutTime - (time() - $data['first_time']))/60);
        $login_error = "Bạn thử quá nhiều lần. Vui lòng chờ {$remain} phút.";
    } elseif ($data['count'] >= $maxAttempts) {
        // Hết thời gian khóa, reset
        unset($_SESSION['login_attempts'][$admin_username]);
        $data = ['count'=>0, 'first_time'=>time()];
    }

    // --- Basic Validation ---
    if (empty($admin_username)) {
        $login_error = "Tên đăng nhập Admin không được để trống.";
    } elseif (empty($admin_password)) {
        $login_error = "Mật khẩu không được để trống.";
    }

    // --- If no basic validation errors ---
    if ($login_error === null) {
        $sql = "SELECT id, admin_username, admin_password, role FROM admin WHERE admin_username = ?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$admin_username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                // Verify password
                if (password_verify($admin_password, $admin['admin_password'])) {
                    // Login successful
                    session_regenerate_id(true); // Security: regenerate session ID

                    // Store necessary admin info in session
                    $_SESSION['admin_id']       = $admin['id'];
                    $_SESSION['admin_username'] = $admin['admin_username'];
                    $_SESSION['admin_role']     = $admin['role']; // Store role if needed for permissions

                    // thêm dòng này để lưu phiên mới
                    recordSession($admin['id']);

                    // Xóa bộ đếm khi login thành công
                    unset($_SESSION['login_attempts'][$admin_username]);

                    // Redirect to admin dashboard
                    header("Location: ".$base_url."public/pages/dashboard/dashboard.php");
                    exit();
                } else {
                    // Incorrect password
                    $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                }
            } else {
                // Admin username not found
                $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            }
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            $login_error = "Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.";
        }
    }

    // --- If login failed or validation error occurred ---
    if ($login_error !== null) {
        // Tăng bộ đếm khi thất bại
        if (!isset($_SESSION['login_attempts'][$admin_username])) {
            $_SESSION['login_attempts'][$admin_username] = [
                'count'      => 1,
                'first_time' => time()
            ];
        } else {
            $_SESSION['login_attempts'][$admin_username]['count']++;
        }
        $_SESSION['admin_login_error'] = $login_error;
        header("Location: ".$base_url."public/pages/auth/admin_login.php");
        exit();
    }

} else {
    // If not a POST request, redirect to admin login page
    header("Location: ".$base_url."public/pages/auth/admin_login.php");
    exit();
}
?>
