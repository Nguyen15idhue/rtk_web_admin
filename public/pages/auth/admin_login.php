<?php
session_start();

// Display error message if set by process_admin_login.php
$error_message = $_SESSION['admin_login_error'] ?? null;
unset($_SESSION['admin_login_error']); // Clear error after displaying

// If admin is already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: ../dashboard.php"); // Adjust path if needed
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Đăng Nhập</title>
    <style>
        /* Reusing similar styles from user login for consistency */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7; /* Slightly different background for admin */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            color: #1a237e; /* Dark blue for admin */
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .form-group input[type="text"], /* Changed from email to text for username */
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #3949ab; /* Darker blue on focus */
            outline: none;
        }
        .btn-login {
            background-color: #303f9f; /* Indigo background */
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #1a237e; /* Darker indigo on hover */
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #ef9a9a;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Đăng Nhập</h2>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="../../../../private/actions/auth/process_admin_login.php" method="POST">
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
