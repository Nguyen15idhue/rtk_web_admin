<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\setting\profile.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php'; // Include the helpers
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// TODO: Fetch actual admin profile data here

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Hồ sơ Quản trị</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-profile" class="content-section">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info Card -->
                <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-200">Thông tin cá nhân</h3>
                    <form onsubmit="updateAdminProfile(event)">
                        <div class="space-y-4">
                            <div>
                                <label for="admin-profile-name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                                <input type="text" id="admin-profile-name" required class="text-sm w-full" value="Super Admin Demo">
                            </div>
                            <div>
                                <label for="admin-profile-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="admin-profile-email" readonly disabled class="text-sm bg-gray-100 cursor-not-allowed w-full" value="super.admin.demo@system.com">
                            </div>
                            <div>
                                <label for="admin-profile-role" class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                                <input type="text" id="admin-profile-role" readonly disabled class="text-sm bg-gray-100 cursor-not-allowed w-full" value="SuperAdmin">
                            </div>
                            <div>
                                <label for="admin-profile-phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                                <input type="tel" id="admin-profile-phone" class="text-sm w-full" placeholder="Chưa cập nhật">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="save-profile-btn" class="btn-primary">
                                <i class="fas fa-save mr-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Card -->
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-200">Đổi mật khẩu</h3>
                    <form onsubmit="changeAdminPassword(event)">
                        <div class="space-y-4">
                            <div>
                                <label for="admin-current-password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-current-password" required class="text-sm w-full">
                            </div>
                            <div>
                                <label for="admin-new-password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-new-password" required class="text-sm w-full">
                                <p class="text-xs text-gray-500 mt-1">Ít nhất 6 ký tự.</p>
                            </div>
                            <div>
                                <label for="admin-confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-confirm-password" required class="text-sm w-full">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="change-password-btn" class="btn-danger">
                                <i class="fas fa-key mr-1"></i> Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function updateAdminProfile(event) {
        event.preventDefault();
        // Add AJAX logic to update profile
        console.log('Updating admin profile...');
        // Show success/error message
    }

    function changeAdminPassword(event) {
        event.preventDefault();
        // Add AJAX logic to change password
        console.log('Changing admin password...');
        // Show success/error message
    }
</script>
