<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\permission_management.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// TODO: Fetch actual roles and permissions data here
// TODO: Implement logic to check if current user is SuperAdmin for editing
$is_super_admin = true; // Placeholder - replace with actual check

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Quản lý Phân quyền</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-permission-management" class="content-section">
            <?php if (!$is_super_admin): ?>
            <p class="text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 p-3 rounded-md mb-4">Chỉ Super Admin mới có thể chỉnh sửa quyền.</p>
            <?php else: ?>
            <p class="text-xs sm:text-sm text-gray-600 mb-4">Chỉ Super Admin mới có thể chỉnh sửa quyền.</p>
            <?php endif; ?>

            <div class="space-y-6">
                <!-- Role: Admin -->
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Vai trò: Quản trị viên (Admin)</h3>
                        <?php if ($is_super_admin): ?>
                        <button class="btn-primary" onclick="savePermissions('Admin', event)" data-permission="permission_edit">
                            <i class="fas fa-save mr-1"></i> Lưu quyền Admin
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-3 text-sm">
                        <div class="flex items-center">
                            <input type="checkbox" id="perm-admin-dashboard" data-role="Admin" data-permission="dashboard" checked disabled data-fixed-disabled class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2 cursor-not-allowed">
                            <label for="perm-admin-dashboard" class="text-gray-700 cursor-not-allowed">Xem Dashboard</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="perm-admin-user_management" data-role="Admin" data-permission="user_management" checked class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2" <?php if (!$is_super_admin) echo 'disabled'; ?>>
                            <label for="perm-admin-user_management" class="text-gray-700">QL người dùng (KH)</label>
                        </div>
                         <div class="flex items-center">
                            <input type="checkbox" id="perm-admin-user_create" data-role="Admin" data-permission="user_create" checked class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2" <?php if (!$is_super_admin) echo 'disabled'; ?>>
                            <label for="perm-admin-user_create" class="text-gray-700">Tạo người dùng (KH)</label>
                        </div>
                        <!-- ... other permissions ... -->
                        <div class="flex items-center">
                            <input type="checkbox" id="perm-admin-settings" data-role="Admin" data-permission="settings" checked disabled data-fixed-disabled class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2 cursor-not-allowed">
                            <label for="perm-admin-settings" class="text-gray-700 cursor-not-allowed">Cài đặt tài khoản</label>
                        </div>
                    </div>
                </div>

                <!-- Role: Operator -->
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-800">Vai trò: Vận hành (Operator)</h3>
                         <?php if ($is_super_admin): ?>
                        <button class="btn-primary" onclick="savePermissions('Operator', event)" data-permission="permission_edit">
                            <i class="fas fa-save mr-1"></i> Lưu quyền Operator
                        </button>
                         <?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-3 text-sm">
                         <div class="flex items-center">
                            <input type="checkbox" id="perm-op-dashboard" data-role="Operator" data-permission="dashboard" checked disabled data-fixed-disabled class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2 cursor-not-allowed">
                            <label for="perm-op-dashboard" class="text-gray-700 cursor-not-allowed">Xem Dashboard</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="perm-op-user_management" data-role="Operator" data-permission="user_management" checked class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2" <?php if (!$is_super_admin) echo 'disabled'; ?>>
                            <label for="perm-op-user_management" class="text-gray-700">QL người dùng (KH)</label>
                        </div>
                         <!-- ... other permissions for Operator ... -->
                         <div class="flex items-center">
                            <input type="checkbox" id="perm-op-settings" data-role="Operator" data-permission="settings" checked disabled data-fixed-disabled class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mr-2 cursor-not-allowed">
                            <label for="perm-op-settings" class="text-gray-700 cursor-not-allowed">Cài đặt tài khoản</label>
                        </div>
                         <!-- ... more permissions ... -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function savePermissions(role, event) {
        event.preventDefault();
        if (!<?php echo json_encode($is_super_admin); ?>) {
            alert('Bạn không có quyền thực hiện hành động này.');
            return;
        }

        const permissions = {};
        document.querySelectorAll(`input[type="checkbox"][data-role="${role}"]`).forEach(checkbox => {
            // Only include permissions that are editable or fixed-disabled
            if (!checkbox.disabled || checkbox.hasAttribute('data-fixed-disabled')) {
                 permissions[checkbox.dataset.permission] = checkbox.checked;
            }
        });

        console.log(`Saving permissions for role ${role}:`, permissions);
        // Add AJAX logic here to send permissions to the server
        // Show success/error message
    }

    // Disable checkboxes if not SuperAdmin on page load
    if (!<?php echo json_encode($is_super_admin); ?>) {
        document.querySelectorAll('#admin-permission-management input[type="checkbox"]:not([data-fixed-disabled])').forEach(cb => {
            cb.disabled = true;
            cb.style.cursor = 'not-allowed';
            const label = document.querySelector(`label[for="${cb.id}"]`);
            if (label) {
                label.style.cursor = 'not-allowed';
                label.style.color = '#6b7280'; // Dim the label
            }
        });
    }
</script>
