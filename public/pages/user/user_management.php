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

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/user_helpers.php'; // User-specific helpers
require_once BASE_PATH . '/actions/user/fetch_users.php'; // User fetching logic

// --- Get Filters ---
$filters = [
    'q' => isset($_GET['q']) ? (string)$_GET['q'] : '', // Use raw input, UserModel will trim
    'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];
// Debug PHP error log
error_log('[DEBUG] Search keyword: ' . $filters['q']);
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

// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Người dùng';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<!-- Main Content Wrapper -->
<main class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div id="admin-user-management" class="content-section">
        <div class="header-actions">
             <h3>Quản lý người dùng (KH)</h3>
            <?php if ($admin_role !== 'customercare'): ?>
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
            <div class="bulk-actions-bar" style="margin-bottom: 15px; display: flex; gap: 10px;">
                <button type="submit" name="export_selected" class="btn btn-info"><i class="fas fa-file-excel"></i> Xuất mục đã chọn</button>
                <button type="submit" name="export_all" class="btn btn-success"><i class="fas fa-file-excel"></i> Xuất tất cả</button>
                <button type="button" id="bulkToggleStatusBtn" onclick="UserManagementPageEvents.bulkToggleUserStatus()" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Đảo trạng thái</button>
            </div>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="usersTable">
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
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td class="text-center"><?php echo get_user_status_display($user); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <button type="button" class="btn-icon btn-view" title="Xem chi tiết" onclick="UserManagementPageEvents.viewUserDetails('<?php echo htmlspecialchars($user['id']); ?>')"><i class="fas fa-eye"></i></button>
                                            <?php if ($admin_role !== 'customercare'): ?>
                                                <button type="button" class="btn-icon btn-edit" title="Sửa" onclick="UserManagementPageEvents.openEditUserModal('<?php echo htmlspecialchars($user['id']); ?>')" data-permission="user_edit"><i class="fas fa-pencil-alt"></i></button>
                                                <?php
                                                    $is_inactive = isset($user['deleted_at']) && $user['deleted_at'] !== null && $user['deleted_at'] !== '';
                                                    $action = $is_inactive ? 'enable' : 'disable';
                                                    $icon = $is_inactive ? 'fa-toggle-on' : 'fa-toggle-off';
                                                    $title = $is_inactive ? 'Kích hoạt' : 'Vô hiệu hóa';
                                                    $btn_class = $is_inactive ? 'btn-success' : 'btn-secondary';
                                                ?>
                                                <button class="btn-icon <?php echo $btn_class; ?>" onclick="UserManagementPageEvents.toggleUserStatus('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo $action; ?>')" title="<?php echo $title; ?>">
                                                    <i class="fas <?php echo $icon; ?>"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="11">Không tìm thấy người dùng phù hợp.</td>
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
        baseUrl: '<?php echo rtrim($base_url, '/'); ?>'
    };
</script>

<!-- load JS utilities and page logic -->
<script src="<?php echo $base_url; ?>public/assets/js/pages/user/user_management.js"></script>

<?php
include $private_layouts_path . 'admin_footer.php';
?>