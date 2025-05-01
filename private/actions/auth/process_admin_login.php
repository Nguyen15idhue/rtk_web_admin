<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php'; // Adjust path as needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = trim($_POST['admin_username'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    $login_error = null;

    // --- Basic Validation ---
    if (empty($admin_username)) {
        $login_error = "Tên đăng nhập Admin không được để trống.";
    } elseif (empty($admin_password)) {
        $login_error = "Mật khẩu không được để trống.";
    }

    // --- If no basic validation errors ---
    if ($login_error === null) {
        // Prepare statement to get admin info based on username
        $sql = "SELECT id, admin_username, admin_password, role FROM admin WHERE admin_username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Admin login prepare statement failed: " . $conn->error);
            $login_error = "Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.";
        } else {
            $stmt->bind_param("s", $admin_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();

                // Verify password
                if (password_verify($admin_password, $admin['admin_password'])) {
                    // Login successful
                    session_regenerate_id(true); // Security: regenerate session ID

                    // Store necessary admin info in session
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['admin_username'];
                    $_SESSION['admin_role'] = $admin['role']; // Store role if needed for permissions

                    // Optional: Log admin login activity
                    // log_activity($conn, $admin['id'], 'admin_login', 'admin', $admin['id']);

                    // Close statement and connection
                    $stmt->close();
                    $conn->close();

                    // Redirect to admin dashboard
                    header("Location: ../../../public/pages/dashboard/dashboard.php"); // Adjust path if needed
                    exit();
                } else {
                    // Incorrect password
                    $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                }
            } else {
                // Admin username not found
                $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            }
            $stmt->close();
        }
    }

    // --- If login failed or validation error occurred ---
    if ($login_error !== null) {
        $_SESSION['admin_login_error'] = $login_error;
        if ($conn && $conn->ping()) { // Check if connection is still alive before closing
             $conn->close();
        }
        header("Location: ../../../public/pages/auth/admin_login.php"); // Redirect back to admin login page
        exit();
    }

     if ($conn && $conn->ping()) {
        $conn->close();
     }

} else {
    // If not a POST request, redirect to admin login page
    header("Location: ../../../public/pages/auth/admin_login.php");
    exit();
}
?>
