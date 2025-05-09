<?php
require_once dirname(__DIR__, 3) . '/private/config/constants.php';

// Display error message if set by process_admin_login.php
$error_message = $_SESSION['admin_login_error'] ?? null;
unset($_SESSION['admin_login_error']); // Clear error after displaying

// If admin is already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    // chuyển đến đúng file dashboard trong pages
    header('Location: ' . BASE_URL . 'public/pages/dashboard/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Đăng Nhập</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/pages/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Đăng Nhập</h2>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>public/handlers/auth/index.php?action=process_admin_login" method="POST">
            <div class="form-group">
                <label for="admin_username">Tên đăng nhập Admin:</label>
                <input type="text" id="admin_username" name="admin_username" required>
            </div>
            <div class="form-group">
                <label for="admin_password">Mật khẩu:</label>
                <input type="password" id="admin_password" name="admin_password" required>
            </div>
            <button type="submit" class="btn-login">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
