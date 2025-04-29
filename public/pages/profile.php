<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\profile.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php'; // Include the helpers
require_once __DIR__ . '/../../private/classes/Database.php'; // Include Database class

// --- Base Path Calculation ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
$project_folder_index = array_search('rtk_web_admin', $script_name_parts);
$base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
$base_path = $protocol . $host . $base_path_segment;

// --- Define Includes Path ---
$private_includes_path = __DIR__ . '/../../private/includes/';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Use the calculated base_path for redirection
    header('Location: ' . $base_path . 'public/pages/auth/admin_login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
// Use 'admin_name' consistent with user_management.php if available, otherwise fallback
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';

// Fetch admin profile data
$db = Database::getInstance();
$conn = $db->getConnection();
$admin_profile = null;

if ($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, name, admin_username, role FROM admin WHERE id = :id");
        $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        $admin_profile = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching admin profile: " . $e->getMessage());
        $admin_profile = null; // Ensure profile is null on error
    } finally {
        $db->close(); // Close connection
    }
} else {
    error_log("Failed to connect to database for admin profile.");
}

// Set default values if profile fetch failed or returned no data
$profile_name = $admin_profile['name'] ?? 'N/A';
$profile_username = $admin_profile['admin_username'] ?? 'N/A'; // Use fetched username
$profile_role = $admin_profile['role'] ?? 'N/A';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Quản trị - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-profile" class="content-section">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info Card -->
                <div class="lg:col-span-2">
                    <h3>Thông tin cá nhân</h3>
                    <form id="admin-profile-form" onsubmit="updateAdminProfile(event)">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-profile-name">Họ tên <span class="text-red-500">*</span></label>
                                <input type="text" id="admin-profile-name" name="name" required value="<?php echo htmlspecialchars($profile_name); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-email">Email (Tên đăng nhập)</label>
                                <input type="text" id="admin-profile-email" name="admin_username" readonly disabled value="<?php echo htmlspecialchars($profile_username); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-role">Vai trò</label>
                                <input type="text" id="admin-profile-role" name="role" readonly disabled value="<?php echo htmlspecialchars(ucfirst($profile_role)); // Capitalize first letter ?>">
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
                    <form id="admin-password-form" onsubmit="changeAdminPassword(event)">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-current-password">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-current-password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="admin-new-password">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-new-password" name="new_password" required minlength="6">
                                <p class="text-xs">Ít nhất 6 ký tự.</p>
                            </div>
                            <div class="form-group">
                                <label for="admin-confirm-password">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-confirm-password" name="confirm_password" required>
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
        const basePath = '<?php echo $base_path; ?>';
    </script>
    <script src="<?php echo $base_path; ?>public/assets/js/pages/setting/profile.js"></script>

</body>
</html>
