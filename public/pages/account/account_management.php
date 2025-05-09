<?php
// filepath: public\pages\account\account_management.php

// --- Bootstrap and Initialization ---
// Includes session start, auth check, DB connection, base path, etc.
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db = $bootstrap_data['db'];
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$private_actions_path = $bootstrap_data['private_actions_path'];

if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

// --- Include Page-Specific Logic ---
// Handles filtering, pagination, and data fetching for accounts
$account_list_data = require $private_actions_path . 'account/handle_account_list.php';
$filters = $account_list_data['filters'];
$accounts = $account_list_data['accounts'];
$total_items = $account_list_data['total_items'];
$total_pages = $account_list_data['total_pages'];
$current_page = $account_list_data['current_page'];
$items_per_page = $account_list_data['items_per_page'];
$pagination_base_url = $account_list_data['pagination_base_url'];

// Fetch provinces list for create account form
$locationsStmt = $db->query("SELECT id, province FROM location WHERE status = 1 ORDER BY province");
$locations = $locationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch package list for create account form
$packagesStmt = $db->query("SELECT id, name FROM package WHERE is_active = 1 ORDER BY display_order");
$packages = $packagesStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Include Helpers needed for the View ---
require_once BASE_PATH . '/utils/dashboard_helpers.php';

// Thiết lập tiêu đề trang để admin_header.php dùng
$page_title = 'Quản lý TK Đo đạc - Admin';

// Include header & sidebar (đã chứa <!DOCTYPE html>, <head> và CSS)
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2>Quản lý TK Đo đạc</h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo $user_display_name; // Already HTML-escaped in bootstrap ?></span>!</span>
            <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>

    <div id="admin-account-management" class="content-section">
        <div class="header-actions">
            <h3>Danh sách tài khoản đo đạc</h3>
            <button class="btn btn-primary" onclick="openCreateMeasurementAccountModal()" data-permission="account_create">
                <i class="fas fa-plus"></i> Tạo TK thủ công
            </button>
        </div>
        <p class="text-xs sm:text-sm text-gray-600 mb-4 description-text">Quản lý các tài khoản dịch vụ đo đạc RTK của khách hàng.</p>

        <!-- Filter Form -->
        <form method="GET" action="">
            <div class="filter-bar">
                <input type="search" placeholder="Tìm ID TK, Username, Email..." name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                <select name="package">
                    <option value="">Tất cả gói</option>
                    <?php foreach ($packages as $pkg): ?>
                        <option value="<?php echo htmlspecialchars($pkg['id']); ?>"
                            <?php echo (isset($filters['package']) && $filters['package'] == $pkg['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pkg['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo (($filters['status'] ?? '') == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="pending" <?php echo (($filters['status'] ?? '') == 'pending') ? 'selected' : ''; ?>>Chờ KH</option>
                    <option value="expired" <?php echo (($filters['status'] ?? '') == 'expired') ? 'selected' : ''; ?>>Hết hạn</option>
                    <option value="suspended" <?php echo (($filters['status'] ?? '') == 'suspended') ? 'selected' : ''; ?>>Đình chỉ</option>
                    <option value="rejected" <?php echo (($filters['status'] ?? '') == 'rejected') ? 'selected' : ''; ?>>Bị từ chối</option>
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-times"></i> Xóa lọc</a>
            </div>
        </form>

        <!-- Bulk Actions and Table -->
        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="accounts">
            <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px;">
                <button type="submit" name="export_selected" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                </button>
                <button type="submit" name="export_all" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất tất cả
                </button>
                <button type="button" id="bulkToggleStatusBtn" onclick="AccountManagementPageEvents.bulkToggleStatus()" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Đảo trạng thái
                </button>
                <button type="button" id="bulkDeleteBtn" onclick="AccountManagementPageEvents.bulkDeleteAccounts()" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Xóa mục đã chọn
                </button>
                <!-- Add bulk renew button -->
                <button type="button" id="bulkRenewBtn" onclick="AccountManagementPageEvents.bulkRenewAccounts()" class="btn btn-info">
                    <i class="fas fa-history"></i> Gia hạn mục đã chọn
                </button>
            </div>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="accountsTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID TK</th>
                            <th>Username TK</th>
                            <th>Email user</th>
                            <th>Gói</th>
                            <th>Ngày KH</th>
                            <th>Ngày HH</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="actions text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($accounts)): ?>
                            <?php foreach ($accounts as $account): ?>
                                <tr data-account-id="<?php echo htmlspecialchars($account['id']); ?>" data-status="<?php echo htmlspecialchars($account['derived_status']); ?>">
                                    <td>
                                        <input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo htmlspecialchars($account['id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($account['id']); ?></td>
                                    <td><?php echo htmlspecialchars($account['username_acc'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($account['user_email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($account['package_name'] ?? ''); ?></td>
                                    <td><?php echo format_date($account['activation_date'] ?? null); ?></td>
                                    <td><?php echo format_date($account['expiry_date'] ?? null); ?></td>
                                    <td class="status"><?php echo get_account_status_badge($account['derived_status'] ?? 'unknown'); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <?php 
                                            // Original buttons from helper
                                            echo get_account_action_buttons($account); 
                                            // Add Renew button manually here or modify get_account_action_buttons helper
                                            $canRenew = !in_array($account['derived_status'], ['pending', 'rejected']); // Example condition
                                            if ($canRenew):
                                            ?>
                                            <button type="button" title="Gia hạn TK" class="btn-icon btn-renew" onclick="AccountManagementPageEvents.openRenewAccountModal('<?php echo htmlspecialchars($account['id']); ?>')">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="9">Không tìm thấy tài khoản phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="pagination-footer">
            <div class="pagination-info">
                <?php if ($total_items > 0):
                    $start_item = ($current_page - 1) * $items_per_page + 1;
                    $end_item = min($start_item + $items_per_page - 1, $total_items);
                ?>
                    Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> tài khoản
                <?php else: ?>
                    Không có tài khoản nào
                <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
            <div class="pagination-controls">
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                <?php
                    $max_pages_to_show = 5;
                    $start_page = max(1, $current_page - floor($max_pages_to_show / 2));
                    $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
                    if ($end_page - $start_page + 1 < $max_pages_to_show) {
                        $start_page = max(1, $end_page - $max_pages_to_show + 1);
                    }

                    if ($start_page > 1) {
                        echo '<button onclick="window.location.href=\'' . $pagination_base_url . 'page=1\'">1</button>';
                        if ($start_page > 2) echo '<span>...</span>';
                    }

                    for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . $i; ?>'"><?php echo $i; ?></button>
                <?php
                    endfor;

                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) echo '<span>...</span>';
                        echo '<button onclick="window.location.href=\'' . $pagination_base_url . 'page=' . $total_pages . '\'">' . $total_pages . '</button>';
                    }
                ?>
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div id="viewAccountModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Chi tiết tài khoản</h4>
            <span class="modal-close" onclick="closeModal('viewAccountModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewAccountDetailsContent">
            <p>Đang tải...</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('viewAccountModal')">Đóng</button>
        </div>
    </div>
</div>

<div id="createAccountModal" class="modal">
    <div class="modal-content">
        <form id="createAccountForm">
            <div class="modal-header">
                <h4>Tạo tài khoản đo đạc mới</h4>
                <span class="modal-close" onclick="closeModal('createAccountModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="create-username">Username TK:</label>
                    <input type="text" id="create-username" name="username_acc" required>
                </div>
                <div class="form-group">
                    <label for="create-password">Mật khẩu TK:</label>
                    <input type="password" id="create-password" name="password_acc" required>
                </div>
                <div class="form-group">
                    <label for="create-user-email">Email User:</label>
                    <input type="email" id="create-user-email" name="user_email" required list="emailSuggestionsCreate">
                    <datalist id="emailSuggestionsCreate"></datalist>
                    <div id="create-user-info" class="user-info"></div>
                </div>
                <div class="form-group">
                    <label for="create-location">Tỉnh/Thành:</label>
                    <select id="create-location" name="location_id" required>
                        <option value="">Chọn tỉnh/thành</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc['id']); ?>">
                                <?php echo htmlspecialchars($loc['province']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create-package">Gói:</label>
                    <select id="create-package" name="package_id">
                        <option value="">Chọn gói</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?php echo $pkg['id']; ?>">
                                <?php echo htmlspecialchars($pkg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create-activation-date">Ngày kích hoạt:</label>
                    <input type="date" id="create-activation-date" name="start_time">
                </div>
                <div class="form-group">
                    <label for="create-expiry-date">Ngày hết hạn:</label>
                    <input type="date" id="create-expiry-date" name="end_time">
                </div>
                <div class="form-group">
                    <label for="create-status">Trạng thái:</label>
                    <select id="create-status" name="status">
                        <option value="active">Hoạt động</option>
                        <option value="pending">Chờ KH</option>
                        <option value="suspended">Đình chỉ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create-account-count">Số lượng TK:</label>
                    <input type="number" id="create-account-count" name="account_count" min="1" value="1">
                </div>
                <div class="form-group error-message" id="createAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createAccountModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
            </div>
        </form>
    </div>
</div>

<div id="editAccountModal" class="modal">
    <div class="modal-content">
        <form id="editAccountForm">
            <div class="modal-header">
                <h4>Chỉnh sửa tài khoản</h4>
                <span class="modal-close" onclick="closeModal('editAccountModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-account-id" name="id">
                <div class="form-group">
                    <label for="edit-username">Username TK:</label>
                    <input type="text" id="edit-username" name="username_acc" required readonly>
                </div>
                <div class="form-group">
                    <label for="edit-password">Mật khẩu TK (Để trống nếu không đổi):</label>
                    <input type="password" id="edit-password" name="password_acc">
                </div>
                <div class="form-group">
                    <label for="edit-user-email">Email User:</label>
                    <input type="email" id="edit-user-email" name="user_email" required list="emailSuggestionsEdit">
                    <datalist id="emailSuggestionsEdit"></datalist>
                    <div id="edit-user-info" class="user-info"></div>
                </div>
                <div class="form-group">
                    <label for="edit-location">Tỉnh/Thành:</label>
                    <select id="edit-location" name="location_id" required>
                        <option value="">Chọn tỉnh/thành</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc['id']); ?>">
                                <?php echo htmlspecialchars($loc['province']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-package">Gói:</label>
                    <select id="edit-package" name="package_id">
                        <option value="">Chọn gói</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?php echo $pkg['id']; ?>">
                                <?php echo htmlspecialchars($pkg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-activation-date">Ngày kích hoạt:</label>
                    <input type="date" id="edit-activation-date" name="activation_date">
                </div>
                <div class="form-group">
                    <label for="edit-expiry-date">Ngày hết hạn:</label>
                    <input type="date" id="edit-expiry-date" name="expiry_date">
                </div>
                <div class="form-group">
                    <label for="edit-status">Trạng thái:</label>
                    <select id="edit-status" name="status" required>
                        <option value="active">Hoạt động</option>
                        <option value="suspended">Đình chỉ</option>
                        <option value="rejected">Bị từ chối</option>
                    </select>
                </div>
                 <div class="form-group error-message" id="editAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editAccountModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div id="renewAccountModal" class="modal">
    <div class="modal-content">
        <form id="renewAccountForm">
            <div class="modal-header">
                <h4>Gia hạn tài khoản</h4>
                <span class="modal-close" onclick="closeModal('renewAccountModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="renew-account-id" name="id">
                <div class="form-group">
                    <label>Username TK:</label>
                    <p id="renew-username-display" class="form-control-static"></p>
                </div>
                <div class="form-group">
                    <label>Gói hiện tại:</label>
                    <p id="renew-current-package-display" class="form-control-static"></p>
                </div>
                <div class="form-group">
                    <label>Ngày hết hạn hiện tại:</label>
                    <p id="renew-current-expiry-display" class="form-control-static"></p>
                </div>
                <hr>
                <div class="form-group">
                    <label for="renew-package">Gói mới (nếu thay đổi):</label>
                    <select id="renew-package" name="package_id">
                        <option value="">Giữ gói hiện tại / Chọn gói mới</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?php echo htmlspecialchars($pkg['id']); ?>">
                                <?php echo htmlspecialchars($pkg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="renew-activation-date">Ngày kích hoạt mới (nếu gia hạn khi đã hết hạn):</label>
                    <input type="date" id="renew-activation-date" name="activation_date" required>
                </div>
                <div class="form-group">
                    <label for="renew-expiry-date">Ngày hết hạn mới:</label>
                    <input type="date" id="renew-expiry-date" name="expiry_date" required>
                </div>
                <div class="form-group error-message" id="renewAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('renewAccountModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận Gia hạn</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Renew Modal -->
<div id="bulkRenewModal" class="modal">
    <div class="modal-content">
        <form id="bulkRenewForm">
            <div class="modal-header">
                <h4>Gia hạn hàng loạt</h4>
                <span class="modal-close" onclick="closeModal('bulkRenewModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulk-renew-package">Chọn gói gia hạn:</label>
                    <select id="bulk-renew-package" name="package_id" required>
                        <option value="">-- Chọn gói --</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?php echo htmlspecialchars($pkg['id']); ?>">
                                <?php echo htmlspecialchars($pkg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group error-message" id="bulkRenewError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('bulkRenewModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận Gia hạn</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div> <!-- Toast container -->

<script>
    const apiBasePath = '<?php echo $base_url; ?>public/handlers/account/index.php';
    const basePath = '<?php echo $base_url; ?>';
    const packageDurations = <?php echo json_encode([
        '1' => ['months' => 1],
        '2' => ['months' => 3],
        '3' => ['months' => 6],
        '4' => ['years' => 1],
        '5' => ['years' => 100],
        '7' => ['days' => 7]
    ]); ?>;
    // expose package list for bulk renew UI
    const packagesList = <?php echo json_encode($packages); ?>;
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/account/account_management.js"></script>

</main>

<?php
include $private_layouts_path . 'admin_footer.php';
?>