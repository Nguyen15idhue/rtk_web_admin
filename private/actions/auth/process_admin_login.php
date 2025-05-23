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
    // CSRF protection: verify token
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token_admin_login']) ||
        !hash_equals($_SESSION['csrf_token_admin_login'], $_POST['csrf_token'])) {
        // Invalid CSRF token
        $_SESSION['admin_login_error'] = 'Yêu cầu không hợp lệ.';
        header("Location: " . $base_url . "public/pages/auth/admin_login.php");
        exit();
    }

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
                    // Unset CSRF token to prevent reuse
                    unset($_SESSION['csrf_token_admin_login']);
                    session_regenerate_id(true); // Security: regenerate session ID

                    // Store necessary admin info in session
                    $_SESSION['admin_id']       = $admin['id'];
                    $_SESSION['admin_username'] = $admin['admin_username'];
                    $_SESSION['admin_role']     = $admin['role'];

                    // Fetch and store permissions in session
                    $_SESSION['admin_permissions'] = []; // Default to empty array
                    if (!empty($admin['role'])) {
                        try {
                            $permissions_sql = "SELECT permission FROM `role_permissions` WHERE `role` = :role AND `allowed` = 1";
                            $permissions_stmt = $db->prepare($permissions_sql);
                            $permissions_stmt->bindParam(':role', $admin['role'], PDO::PARAM_STR);
                            $permissions_stmt->execute();
                            $user_permissions = $permissions_stmt->fetchAll(PDO::FETCH_COLUMN);
                            if ($user_permissions) {
                                $_SESSION['admin_permissions'] = $user_permissions;
                            } else {
                                // No permissions found for the role, or all are disallowed.
                                // This might be a configuration issue or an intentional setup.
                                // For now, proceed with empty permissions in session.
                                // If specific roles *must* have permissions, this could be an error.
                                error_log("No active permissions found for role {$admin['role']} during login. User will have no permissions via session.");
                            }
                        } catch (PDOException $e) {
                            error_log("Critical: Error fetching permissions for role {$admin['role']} during login: " . $e->getMessage());
                            // Prevent login if permissions cannot be loaded
                            $login_error = "Không thể tải thông tin phân quyền. Vui lòng thử lại hoặc liên hệ quản trị viên.";
                            // Clean up session variables set so far for this attempt
                            unset($_SESSION['admin_id']);
                            unset($_SESSION['admin_username']);
                            unset($_SESSION['admin_role']);
                            unset($_SESSION['admin_permissions']); // Ensure it's cleared
                            // No session_regenerate_id() was called yet if we are here before successful permission load.
                            // The CSRF token for login should remain for a retry.
                        }
                    }

                    // If there was an error fetching permissions, redirect back to login
                    if ($login_error !== null) {
                        $_SESSION['admin_login_error'] = $login_error;
                        header("Location: " . $base_url . "public/pages/auth/admin_login.php");
                        exit();
                    }

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
