<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\user_management.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// TODO: Fetch actual user data here

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Quản lý Người dùng</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-user-management" class="content-section">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3 md:gap-2">
                <h3 class="text-lg md:text-xl font-semibold text-gray-900">Quản lý người dùng (KH)</h3>
                <button class="btn-primary self-start md:self-auto w-full md:w-auto" onclick="openCreateUserModal()" data-permission="user_create">
                    <i class="fas fa-plus mr-1"></i> Thêm người dùng
                </button>
            </div>
            <p class="text-xs sm:text-sm text-gray-600 mb-4">Quản lý tài khoản người dùng đăng ký (không phải tài khoản quản trị).</p>

            <div class="mb-4 flex flex-wrap gap-2 items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                <input type="search" placeholder="Tìm Email, Tên..." class="flex-grow min-w-[180px] text-sm">
                <select class="min-w-[150px] text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Bị khóa</option>
                </select>
                <button class="btn-secondary"><i class="fas fa-search mr-1"></i> Tìm</button>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr>
                                <th class="w-1/12 px-2 py-2">ID</th>
                                <th class="w-3/12 px-2 py-2">Họ tên</th>
                                <th class="w-3/12 px-2 py-2">Email</th>
                                <th class="w-2/12 px-2 py-2">Ngày ĐK</th>
                                <th class="w-1/12 px-2 py-2">Trạng thái</th>
                                <th class="w-2/12 px-2 py-2 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Sample Row 1 -->
                            <tr>
                                <td class="px-2 py-1">U001</td>
                                <td class="px-2 py-1">Người dùng Demo</td>
                                <td class="px-2 py-1">demo@example.com</td>
                                <td class="px-2 py-1">10/06/2024</td>
                                <td class="px-2 py-1"><span class="badge badge-green">Hoạt động</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem chi tiết" onclick="viewUserDetails('U001')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditUserModal('U001')" data-permission="user_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-red-600 hover:text-red-700" title="Khóa" onclick="toggleUserStatus('U001', event)" data-permission="user_status_toggle"><i class="fas fa-lock text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                            <!-- Sample Row 2 (Locked) -->
                             <tr data-user-id="U002"> <!-- Added data-user-id for easier selection -->
                                <td class="px-2 py-1">U002</td>
                                <td class="px-2 py-1">Another User</td>
                                <td class="px-2 py-1">another@example.com</td>
                                <td class="px-2 py-1">11/06/2024</td>
                                <td class="px-2 py-1"><span class="badge badge-red">Đã khóa</span></td>
                                <td class="px-2 py-1 text-center whitespace-nowrap space-x-1">
                                    <button class="btn-icon" title="Xem chi tiết" onclick="viewUserDetails('U002')"><i class="fas fa-eye text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-yellow-600 hover:text-yellow-700" title="Sửa" onclick="openEditUserModal('U002')" data-permission="user_edit"><i class="fas fa-pencil-alt text-[11px] md:text-xs"></i></button>
                                    <button class="btn-icon text-green-600 hover:text-green-700" title="Mở khóa" onclick="toggleUserStatus('U002', event)" data-permission="user_status_toggle"><i class="fas fa-lock-open text-[11px] md:text-xs"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 flex flex-col sm:flex-row justify-between items-center border-t border-gray-200 bg-gray-50 text-xs gap-2">
                    <div class="text-gray-600">Hiển thị 1-3 của 15 người dùng</div>
                    <div class="flex space-x-1">
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100 disabled:opacity-50" disabled>Trước</button>
                        <button class="px-2 py-1 border border-primary-500 rounded text-white bg-primary-500">1</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">2</button>
                        <button class="px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-100">Sau</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Placeholder functions - implement actual logic with modals/AJAX
    function openCreateUserModal() { console.log('Open create user modal'); }
    function viewUserDetails(userId) { console.log('View details for user:', userId); }
    function openEditUserModal(userId) { console.log('Open edit modal for user:', userId); }
    function toggleUserStatus(userId, event) {
        const button = event.currentTarget;
        const icon = button.querySelector('i');
        const isLocking = icon.classList.contains('fa-lock');
        const action = isLocking ? 'Khóa' : 'Mở khóa';
        if (confirm(`Bạn có chắc muốn ${action.toLowerCase()} người dùng ${userId}?`)) {
            console.log(`${action} user:`, userId);
            // Add AJAX logic here
            // On success, toggle icon and badge
            const row = button.closest('tr'); // Find the table row
            const badge = row.querySelector('.badge'); // Find the badge within the row

            if (isLocking) {
                icon.classList.remove('fa-lock');
                icon.classList.add('fa-lock-open');
                button.title = 'Mở khóa';
                button.classList.remove('text-red-600', 'hover:text-red-700');
                button.classList.add('text-green-600', 'hover:text-green-700');
                badge.textContent = 'Đã khóa';
                badge.className = 'badge badge-red'; // Update badge style
            } else {
                icon.classList.remove('fa-lock-open');
                icon.classList.add('fa-lock');
                button.title = 'Khóa';
                button.classList.remove('text-green-600', 'hover:text-green-700');
                button.classList.add('text-red-600', 'hover:text-red-700');
                badge.textContent = 'Hoạt động';
                badge.className = 'badge badge-green'; // Update badge style
            }
        }
    }
</script>
