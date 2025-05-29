<?php
// File: public/pages/user/user_management.php

$GLOBALS['required_permission'] = 'user_management'; // Added permission requirement

// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                = $bootstrap_data['db'];
$base_path         = $bootstrap_data['base_path'];
$base_url          = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$admin_role        = $bootstrap_data['admin_role'];

// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Người dùng';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/user_helpers.php'; // User-specific helpers
require_once BASE_PATH . '/actions/user/fetch_users.php'; // User fetching logic

// --- NEW: Pass permissions to JS ---
$user_permissions = [
    'user_management_edit' => Auth::can('user_management_edit'),
    // Add other relevant permissions if needed for this page
];
// --- END NEW ---

// --- Get Filters ---
$filters = [
    'q' => isset($_GET['q']) ? (string)$_GET['q'] : '', // Use raw input, UserModel will trim
    'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];
// Debug JavaScript console
echo '<script>console.log("DEBUG Search keyword:", ' . json_encode($filters['q'], JSON_UNESCAPED_UNICODE) . ');</script>';

// --- Pagination Setup ---
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Users ---
$userData = fetch_paginated_users($filters, $current_page, $items_per_page);
$users = $userData['users'];
$total_items = $userData['total_count'];
$total_pages = $userData['total_pages'];
$current_page = $userData['current_page']; // Use the validated page number from the function

// --- Build Pagination URL ---
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
?>

<!-- Main Content Wrapper -->
<main class="content-wrapper">
    <!-- Content Header -->
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <!-- Main Content Section -->
    <div id="admin-user-management" class="content-section">
        <div class="header-actions">
             <h3>Quản lý người dùng (KH)</h3>
            <?php if (Auth::can('user_management_edit')): ?>
                <button class="btn btn-primary" onclick="UserManagementPageEvents.openCreateUserModal()" data-permission="user_create">
                    <i class="fas fa-plus"></i> Thêm người dùng
                </button>
            <?php endif; ?>
        </div>
         <p class="text-xs sm:text-sm text-gray-600 mb-4 description-text">Quản lý tài khoản người dùng đăng ký (không phải tài khoản quản trị).</p>

        <form method="GET" action="">
            <div class="filter-bar">
                <input type="search" placeholder="Tìm Email, Tên, Số ĐT, Mã số thuế..." name="q" value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>">
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo ($filters['status'] == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="inactive" <?php echo ($filters['status'] == 'inactive') ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
            </div>
        </form>

        <!-- Bulk Actions Form -->
        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="users">
            <div class="bulk-actions-bar mb-3">
                <button type="submit" name="export_all" class="btn btn-success"><i class="fas fa-file-excel"></i> Xuất tất cả</button>
                <button type="submit" name="export_selected" value="excel" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                </button>
                <input type="hidden" name="selected_ids" id="selectedUserIdsForExport">
                <?php if (Auth::can('user_management_edit')): ?>
                <button type="button" class="btn btn-warning" onclick="UserManagementPageEvents.bulkToggleUserStatus()" data-permission="user_edit" title="Đảo ngược trạng thái của các mục đã chọn">
                    <i class="fas fa-exchange-alt"></i> Đảo trạng thái hàng loạt
                </button>
                <?php endif; ?>
            </div>

            <div class="table-wrapper">
                <table class="table" id="usersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Loại TK</th>
                            <th>Tên công ty</th>
                            <th>Mã số thuế</th>
                            <th>Địa chỉ công ty</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo htmlspecialchars($user['id']); ?>"></td>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                    <td><?php echo $user['is_company'] ? 'Công ty' : 'Cá nhân'; ?></td>
                                    <td><?php echo $user['is_company'] ? htmlspecialchars($user['company_name'] ?? '-') : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($user['tax_code'] ?? '-'); ?></td>
                                    <td><?php echo $user['is_company'] ? htmlspecialchars($user['company_address'] ?? '-') : '-'; ?></td>
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td class="text-center"><?php echo get_user_status_display($user); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <button type="button" class="btn-icon btn-view" title="Xem chi tiết" onclick="UserManagementPageEvents.viewUserDetails('<?php echo htmlspecialchars($user['id']); ?>')"><i class="fas fa-eye"></i></button>
                                            <?php if (Auth::can('user_management_edit')): ?>
                                            <button type="button" class="btn-icon btn-edit" title="Sửa" onclick="UserManagementPageEvents.openEditUserModal('<?php echo htmlspecialchars($user['id']); ?>')" data-permission="user_edit"><i class="fas fa-pencil-alt"></i></button>
                                            <?php
                                                $is_inactive = isset($user['deleted_at']) && $user['deleted_at'] !== null && $user['deleted_at'] !== '';
                                                $action = $is_inactive ? 'enable' : 'disable';
                                                $icon = $is_inactive ? 'fa-toggle-on' : 'fa-toggle-off';
                                                $title = $is_inactive ? 'Kích hoạt' : 'Vô hiệu hóa';
                                                $btn_class = $is_inactive ? 'btn-success' : 'btn-secondary';
                                            ?>
                                            <button class="btn-icon <?php echo $btn_class; ?>" onclick="UserManagementPageEvents.toggleUserStatus('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo $action; ?>')" title="<?php echo $title; ?>" data-permission="user_edit">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </button>
                                            <button type="button" class="btn-icon btn-password" title="Đổi mật khẩu" onclick="UserManagementPageEvents.openChangePasswordModal('<?php echo htmlspecialchars($user['id']); ?>')" data-permission="user_edit"><i class="fas fa-key"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="12">Không tìm thấy người dùng phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form> <!-- End Bulk Actions Form -->

        <?php include $private_layouts_path . 'pagination.php'; ?>

    </div> <!-- End content-section -->

</main> <!-- End content-wrapper -->

<!-- pass baseUrl into JS -->
<script>
    window.appConfig = {
        baseUrl: '<?php echo rtrim($base_url, '/'); ?>',
        // --- NEW: Add permissions to appConfig ---
        permissions: <?php echo json_encode($user_permissions); ?>
        // --- END NEW ---
    };
</script>

<!-- load JS utilities and page logic -->
<script src="<?php echo $base_url; ?>public/assets/js/pages/user/user_management.js"></script>

<?php
include $private_layouts_path . 'admin_footer.php';
?>