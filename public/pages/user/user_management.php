<?php
// File: public/pages/user/user_management.php

// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$db                = $bootstrap_data['db'];
$base_path         = $bootstrap_data['base_path'];
$base_url          = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_includes_path = $bootstrap_data['private_includes_path'];
$admin_role        = $bootstrap_data['admin_role'];

// authorization check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/functions.php'; // General helpers (includes format_date)
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
$items_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Users ---
$userData = fetch_paginated_users($filters, $current_page, $items_per_page);
$users = $userData['users'];
$total_items = $userData['total_count'];
$total_pages = $userData['total_pages'];
$current_page = $userData['current_page']; // Use the validated page number from the function

// --- Build Pagination URL ---
$pagination_params = $filters; // Start with existing filters
unset($pagination_params['page']);
// Ensure the base URL for pagination doesn't include existing query string if filters are empty
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
if (!empty($pagination_params)) {
    $pagination_base_url .= '?' . http_build_query($pagination_params);
    $pagination_base_url .= '&'; // Add separator for the page param
} else {
    $pagination_base_url .= '?'; // Start query string for the page param
}
// Remove trailing '&' if it exists
$pagination_base_url = rtrim($pagination_base_url, '&');
// Ensure there's a '?' before adding 'page=' if no other params exist
if (strpos($pagination_base_url, '?') === false) {
    $pagination_base_url .= '?';
} else if (substr($pagination_base_url, -1) !== '?') {
    $pagination_base_url .= '&';
}

// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Người dùng';
include $private_includes_path . 'admin_header.php';
include $private_includes_path . 'admin_sidebar.php';
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
        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>private/actions/export_excel.php">
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
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td><?php echo get_user_status_display($user); ?></td>
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
                                <td colspan="10">Không tìm thấy người dùng phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form> <!-- End Bulk Actions Form -->

        <div class="pagination-footer">
             <div class="pagination-info">
                <?php if ($total_items > 0):
                    $start_item = ($current_page - 1) * $items_per_page + 1;
                    $end_item = min($start_item + $items_per_page - 1, $total_items);
                ?>
                    Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> người dùng
                <?php else: ?>
                    Không có người dùng nào
                <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
            <div class="pagination-controls">
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . $i; ?>'"><?php echo $i; ?></button>
                <?php endfor; ?>
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
            </div>
             <?php endif; ?>
        </div>
    </div> <!-- End content-section -->

    <!-- Modals remain inside the main wrapper but outside the primary content section -->
    <!-- View User Modal -->
    <div id="viewUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Chi tiết Người dùng</h4>
                <span class="modal-close" onclick="helpers.closeModal('viewUserModal')">&times;</span>
            </div>
            <div class="modal-body" id="viewUserDetailsBody">
                <p>Đang tải...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="helpers.closeModal('viewUserModal')">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
             <form id="editUserForm">
                <div class="modal-header">
                    <h4>Chỉnh sửa Người dùng</h4>
                    <span class="modal-close" onclick="helpers.closeModal('editUserModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="form-group">
                        <label for="editUsername">Tên đăng nhập</label>
                        <input type="text" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Số điện thoại</label>
                        <input type="tel" id="editPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="editIsCompany" name="is_company" onchange="helpers.toggleCompanyFields('edit')">
                            <label for="editIsCompany">Là tài khoản công ty?</label>
                        </div>
                    </div>
                    <div class="company-fields" id="editCompanyFields">
                        <div class="form-group">
                            <label for="editCompanyName">Tên công ty</label>
                            <input type="text" id="editCompanyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="editTaxCode">Mã số thuế</label>
                            <input type="text" id="editTaxCode" name="tax_code">
                        </div>
                    </div>
                     <div id="editUserError" class="error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="helpers.closeModal('editUserModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="helpers.closeModal('createUserModal')">&times;</span>
            <h2>Thêm người dùng mới</h2>
            <form id="createUserForm">
                 <div class="modal-body"> <!-- Wrap form fields in modal-body -->
                    <div class="form-group">
                        <label for="createUsername">Tên người dùng:</label>
                        <input type="text" id="createUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="createEmail">Email:</label>
                        <input type="email" id="createEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="createPassword">Mật khẩu:</label>
                        <input type="password" id="createPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="createPhone">Số điện thoại:</label>
                        <input type="tel" id="createPhone" name="phone">
                    </div>
                     <div class="form-group form-check">
                        <input type="checkbox" id="createIsCompany" name="is_company" onchange="helpers.toggleCompanyFields('create')">
                        <label for="createIsCompany">Là công ty?</label>
                    </div>
                    <div id="createCompanyFields" class="company-fields">
                        <div class="form-group">
                            <label for="createCompanyName">Tên công ty:</label>
                            <input type="text" id="createCompanyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="createTaxCode">Mã số thuế:</label>
                            <input type="text" id="createTaxCode" name="tax_code">
                        </div>
                    </div>
                     <p id="createUserError" class="error-message"></p>
                 </div> <!-- End modal-body -->
                <div class="form-actions modal-footer"> <!-- Use modal-footer for consistency -->
                     <button type="button" class="btn btn-secondary" onclick="helpers.closeModal('createUserModal')">Hủy</button>
                     <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>

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
include $private_includes_path . 'admin_footer.php';
?>