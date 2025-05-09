<?php
// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$db = $bootstrap_data['db'];
$base_path = $bootstrap_data['base_path'];
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_includes_path = $bootstrap_data['private_includes_path'];

// --- Include Helpers ---
require_once BASE_PATH . '/utils/dashboard_helpers.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Use the calculated base_path for redirection
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Initialize empty placeholders; actual data will be loaded via AJAX
$profile_name = '';
$profile_username = '';
$profile_role = '';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Quản trị - Admin</title>
    <style>
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } /* Adjust breakpoint if needed */
        .gap-6 { gap: 1.5rem; }
        .lg\:col-span-2 { grid-column: span 2 / span 2; }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(1rem * var(--tw-space-y-reverse)); } /* Simplified spacing */
        .mt-6 { margin-top: 1.5rem; }
        .text-red-500 { color: var(--danger-500); } /* For required asterisk */
    </style>
</head>
<body>

    <?php include $private_includes_path . 'admin_header.php'; ?>
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Hồ sơ Quản trị</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-profile" class="content-section">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info Card -->
                <div class="lg:col-span-2">
                    <h3>Thông tin cá nhân</h3>
                    <form id="admin-profile-form" novalidate autocomplete="off">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-profile-name">Họ tên <span class="text-red-500">*</span></label>
                                <input type="text" id="admin-profile-name" name="name" required value="<?php echo htmlspecialchars($profile_name); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-email">Tên đăng nhập</label>
                                <input type="text" id="admin-profile-email" name="admin_username" readonly disabled value="<?php echo htmlspecialchars($profile_username); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-role">Vai trò</label>
                                <input type="text" id="admin-profile-role" name="role" readonly disabled value="<?php echo htmlspecialchars($profile_role); ?>">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="save-profile-btn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <span id="profile-status" class="status-message"></span>
                        </div>
                    </form>
                </div>

                <!-- Change Password Card -->
                <div>
                    <h3>Đổi mật khẩu</h3>
                    <form id="admin-password-form" novalidate autocomplete="off">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-current-password">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-current-password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="admin-new-password">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-new-password" name="new_password" required minlength="6" autocomplete="new-password">
                                <p class="text-xs">Ít nhất 6 ký tự.</p>
                            </div>
                            <div class="form-group">
                                <label for="admin-confirm-password">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-confirm-password" name="confirm_password" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="change-password-btn" class="btn btn-danger">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </button>
                             <span id="password-status" class="status-message"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Expose PHP basePath and load profile JS -->
    <script>
        const basePath = '<?php echo $base_url; ?>';
    </script>
    <script defer src="<?php echo $base_url; ?>public/assets/js/pages/setting/profile.js"></script>

<?php
include $private_includes_path . 'admin_footer.php';
?>