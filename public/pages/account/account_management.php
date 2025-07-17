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

// --- NEW: single unified permission ---
$account_permissions = [
    'account_management_edit' => Auth::can('account_management_edit'),
];

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

// Use data fetched in action handler
$locations = $account_list_data['locations'];
$packages = $account_list_data['packages'];
$packageDurations = $account_list_data['packageDurations'];

// --- Include Helpers needed for the View ---
require_once BASE_PATH . '/utils/dashboard_helpers.php';

// Thiết lập tiêu đề trang để admin_header.php dùng
$page_title = 'Quản lý TK Đo đạc - Admin';
$additional_css = ['pages/account_management.css'];
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="admin-account-management" class="content-section">
        <div class="header-actions">
            <h3>Danh sách tài khoản đo đạc</h3>
            <button class="btn btn-primary" onclick="openCreateMeasurementAccountModal()" data-permission="account_management_edit">
                <i class="fas fa-plus"></i> Tạo TK thủ công
            </button>
            <button id="syncIdsBtn" class="btn btn-secondary" data-permission="account_management_edit">
                <i class="fas fa-sync"></i> Đồng bộ thông tin
            </button>
            <button id="fullSyncBtn" class="btn btn-warning" data-permission="account_management_edit">
                <i class="fas fa-sync-alt"></i> Đồng bộ hoàn toàn
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
                <button type="button" id="bulkToggleStatusBtn" onclick="AccountManagementPageEvents.bulkToggleStatus()" class="btn btn-warning" data-permission="account_management_edit">
                    <i class="fas fa-sync-alt"></i> Đảo trạng thái
                </button>
                <button type="button" id="bulkDeleteBtn" onclick="AccountManagementPageEvents.bulkDeleteAccounts()" class="btn btn-danger" data-permission="account_management_edit">
                    <i class="fas fa-trash"></i> Xóa mục đã chọn
                </button>
                <!-- Add bulk renew button -->
                <button type="button" id="bulkRenewBtn" onclick="AccountManagementPageEvents.bulkRenewAccounts()" class="btn btn-info" data-permission="account_management_edit">
                    <i class="fas fa-history"></i> Gia hạn mục đã chọn
                </button>
            </div>

            <div class="table-wrapper">
                <table class="table" id="accountsTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID TK</th>
                            <th>Username TK</th>
                            <th>Email user</th>
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
                                    <td><?php echo format_date($account['activation_date'] ?? null); ?></td>
                                    <td><?php echo format_date($account['expiry_date'] ?? null); ?></td>
                                    <td class="status"><?php echo get_status_badge('account', $account['derived_status'] ?? 'unknown'); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <?php 
                                                echo get_account_action_buttons($account); 
                                                $canRenew = !in_array($account['derived_status'], ['pending','rejected']);
                                            ?>
                                            <?php if ($canRenew): ?>
                                            <button
                                                type="button"
                                                class="btn-icon btn-renew"
                                                data-permission="account_management_edit"
                                                title="Gia hạn TK"
                                                onclick="AccountManagementPageEvents.openRenewAccountModal('<?php echo htmlspecialchars($account['id']); ?>')">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="8">Không tìm thấy tài khoản phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <?php include $private_layouts_path . 'pagination.php'; ?>

    </div> <!-- End admin-account-management -->
</main>

<div id="viewAccountModal" class="modal">
    <div class="modal-content modern-modal-content">
        <div class="modal-header">
            <h4>Chi tiết tài khoản</h4>
            <span class="modal-close" onclick="closeModal('viewAccountModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewAccountDetailsContent">
            <div id="viewAccountMainDetails">
                <!-- Main account details will be populated here by JS -->
            </div>
            <div id="viewAccountMountpoints" style="margin-top: 15px;">
                <!-- Mountpoints table will be populated here by JS -->
            </div>
            <div id="viewAccountLoading" style="display: none;">
                 <p>Đang tải...</p>
            </div>
            <div id="viewAccountError" style="display: none; color: red;">
                 <!-- Error message will be populated here by JS -->
            </div>
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
                    <input type="email" id="create-user-email" name="user_email" autocomplete="off" required list="emailSuggestionsCreate">
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
                    <input type="date" id="create-activation-date" name="start_time" value="<?php echo date('Y-m-d'); ?>">
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
                    <input type="email" id="edit-user-email" name="user_email" autocomplete="off" required list="emailSuggestionsEdit">
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

<!-- Sync Account IDs Modal -->
<div id="syncIdsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Đồng bộ thông tin tài khoản RTK</h4>
            <span class="modal-close" onclick="closeModal('syncIdsModal')">&times;</span>
        </div>
        <div class="modal-body" id="syncIdsModalBody">
            <p>Đang tìm kiếm thông tin tài khoản cần cập nhật...</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('syncIdsModal')">Hủy</button>
            <button type="button" id="confirmSyncIdsBtn" class="btn btn-primary">Cập nhật thông tin</button>
        </div>
    </div>
</div>

<!-- Full Sync Confirmation Modal -->
<div id="fullSyncModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>⚠️ Đồng bộ hoàn toàn từ API RTK</h4>
            <span class="modal-close" onclick="closeModal('fullSyncModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="warning-message" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <strong>🚨 CẢNH BÁO:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Thao tác này sẽ <strong>sao lưu dữ liệu hiện tại</strong> vào bảng backup</li>
                    <li>Sau đó <strong>XÓA TOÀN BỘ</strong> dữ liệu trong bảng survey_account</li>
                    <li>Và <strong>GHI ĐÈ</strong> bằng dữ liệu từ API RTK</li>
                    <li>Các thông tin bổ sung sẽ được lấy từ backup theo username</li>
                </ul>
            </div>
            <p><strong>Bạn có chắc chắn muốn thực hiện đồng bộ hoàn toàn không?</strong></p>
            <div id="fullSyncStatus" style="display: none; margin-top: 15px;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('fullSyncModal')">Hủy bỏ</button>
            <button type="button" id="confirmFullSyncBtn" class="btn btn-danger">🔄 Xác nhận đồng bộ hoàn toàn</button>
        </div>
    </div>
</div>

<div id="toast-container"></div> <!-- Toast container -->

<!-- expose packagesList and packageDurations before loading the account_management.js -->
<script>
window.packagesList     = <?php echo json_encode($packages,          JSON_UNESCAPED_UNICODE); ?>;
window.packageDurations = <?php echo json_encode($packageDurations, JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- expose permissions -->
<script>
window.appConfig = {
    baseUrl: '<?php echo rtrim($base_url, '/'); ?>',
    permissions: <?php echo json_encode($account_permissions, JSON_UNESCAPED_UNICODE); ?>
};
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/account/account_management.js"></script>

<?php
include $private_layouts_path . 'admin_footer.php';
?>