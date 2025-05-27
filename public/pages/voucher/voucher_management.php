<?php
// filepath: public/pages/voucher/voucher_management.php

// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                = $bootstrap_data['db'];
$base_path         = $bootstrap_data['base_path'];
$base_url          = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$admin_role        = $bootstrap_data['admin_role'];

// authorization check
require_once __DIR__ . '/../../../private/core/auth_check.php';

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/actions/voucher/fetch_vouchers.php';
require_once BASE_PATH . '/classes/LocationModel.php'; // Added
require_once BASE_PATH . '/classes/PackageModel.php'; // Added

// --- Get Filters and Pagination Setup ---
$filters = [
    'q'     => (string)(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'type'  => (string)(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'status'=> (string)(filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'location_id' => (string)(filter_input(INPUT_GET, 'location_id', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'package_id'  => (string)(filter_input(INPUT_GET, 'package_id', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
];
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Vouchers ---
$data = fetch_paginated_vouchers($filters, $page, $items_per_page);
$vouchers = $data['vouchers'];
$total_items = $data['total_count'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];

// --- Fetch Locations and Packages for Filters ---
$locationModel = new LocationModel();
$locations = $locationModel->getAllLocations();

$packageModel = new PackageModel();
$packages = $packageModel->getAllPackages();

// --- Build Pagination URL ---
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
$isEditVoucherAllowed = Auth::can('voucher_management_edit');
// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Voucher';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <!-- Content Header -->
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="voucher-management" class="content-section">
        <div class="header-actions">
            <h3>Quản lý Voucher</h3>
            <div class="action-buttons">
                <a href="<?php echo $base_path; ?>public/pages/voucher/voucher_analytics.php" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Phân tích Voucher
                </a>
                <?php if ($isEditVoucherAllowed): ?>
                <button class="btn btn-primary" onclick="VoucherPage.openCreateModal()"><i class="fas fa-plus"></i> Thêm Voucher</button>
                <?php endif; ?>
            </div>
        </div>
        <form method="GET" action="">
            <div class="filter-bar">
                <input type="search" name="q" placeholder="Tìm mã hoặc mô tả..." value="<?php echo htmlspecialchars($filters['q']); ?>">
                <select name="type">
                    <option value="">Tất cả loại</option>
                    <option value="fixed_discount" <?php echo $filters['type']=='fixed_discount'?'selected':''; ?>>Giảm cố định</option>
                    <option value="percentage_discount" <?php echo $filters['type']=='percentage_discount'?'selected':''; ?>>Giảm phần trăm</option>
                    <option value="extend_duration" <?php echo $filters['type']=='extend_duration'?'selected':''; ?>>Tặng tháng</option>
                </select>
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo $filters['status']=='active'?'selected':''; ?>>Hoạt động</option>
                    <option value="inactive" <?php echo $filters['status']=='inactive'?'selected':''; ?>>Vô hiệu hóa</option>
                    <option value="expired" <?php echo $filters['status']=='expired'?'selected':''; ?>>Hết hạn</option>
                </select>
                <select name="location_id">
                    <option value="">Tất cả tỉnh thành</option>
                    <?php 
                    foreach ($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location['id']); ?>" <?php echo $filters['location_id']==$location['id']?'selected':''; ?>><?php echo htmlspecialchars($location['province']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="package_id">
                    <option value="">Tất cả gói</option>
                    <?php 
                    foreach ($packages as $package): ?>
                        <option value="<?php echo htmlspecialchars($package['id']); ?>" <?php echo $filters['package_id']==$package['id']?'selected':''; ?>><?php echo htmlspecialchars($package['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa lọc</a>
            </div>
        </form>

        <form id="bulkActionForm" method="POST" action="<?php echo $base_path; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="vouchers">
            <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px;">
                <button type="submit" name="export_selected" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                </button>
                <button type="submit" name="export_all" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất tất cả
                </button>
                <?php if ($isEditVoucherAllowed): ?>
                <button type="button" id="bulkToggleStatusBtn" onclick="VoucherPage.bulkToggleStatus()" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Đảo trạng thái
                </button>
                <button type="button" id="bulkDeleteBtn" onclick="VoucherPage.bulkDeleteVouchers()" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Xóa mục đã chọn
                </button>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Mã</th>
                            <th>Mô tả</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Giới hạn</th>
                            <th>Đơn hàng tối thiểu</th>
                            <th>Số lượng</th>
                            <th>Đã dùng</th>
                            <th>SL TK Tối đa</th>
                            <th>Tỉnh áp dụng</th>
                            <th>Gói áp dụng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vouchers)): ?>
                            <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo htmlspecialchars($v['id']); ?>"></td>
                                <td><?php echo htmlspecialchars($v['id']); ?></td>
                                <td><?php echo htmlspecialchars($v['code']); ?></td>
                                <td><?php echo htmlspecialchars($v['description']); ?></td>
                                <td><?php echo htmlspecialchars(get_voucher_type_display($v['voucher_type'])); ?></td>
                                <td><?php
                                    if ($v['voucher_type'] === 'percentage_discount') {
                                        echo htmlspecialchars($v['discount_value'] . '%');
                                    } elseif ($v['voucher_type'] === 'extend_duration') {
                                        echo htmlspecialchars($v['discount_value'] . ' tháng');
                                    } else {
                                        echo htmlspecialchars(format_currency($v['discount_value']));
                                    }
                                ?></td>
                                <td><?php echo htmlspecialchars($v['max_discount'] ? format_currency($v['max_discount']) : '-'); ?></td>
                                <td><?php echo htmlspecialchars($v['min_order_value'] ? format_currency($v['min_order_value']) : '-'); ?></td>
                                <td><?php echo htmlspecialchars($v['quantity'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($v['used_quantity']); ?></td>
                                <td><?php echo htmlspecialchars($v['max_sa'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($v['location_name'] ?? 'Tất cả'); ?></td>
                                <td><?php echo htmlspecialchars($v['package_name'] ?? 'Tất cả'); ?></td>
                                <td><?php echo format_date($v['start_date']); ?> - <?php echo format_date($v['end_date']); ?></td>
                                <td><?php 
                                    if (strtotime($v['end_date']) < time()) {
                                        echo get_status_badge('voucher', 'expired');
                                    } else {
                                        echo get_status_badge('voucher', $v['is_active'] ? 'active' : 'inactive');
                                    }
                                ?></td>
                                <td>
                                    <button type="button" class="btn-icon btn-view" title="Xem chi tiết" onclick="VoucherPage.viewDetails(<?php echo $v['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <?php if ($isEditVoucherAllowed): ?>
                                    <button type="button" class="btn-icon btn-edit" title="Chỉnh sửa" onclick="VoucherPage.openEditModal(<?php echo $v['id']; ?>)"><i class="fas fa-pencil-alt"></i></button>
                                    <button type="button" class="btn-icon btn-success" title="Nhân bản voucher" onclick="VoucherPage.cloneVoucher(<?php echo $v['id']; ?>)"><i class="fas fa-copy"></i></button>
                                    <button type="button" class="btn-icon" title="<?php echo $v['is_active'] ? 'Vô hiệu hóa' : 'Kích hoạt'; ?>" onclick="VoucherPage.toggleStatus(<?php echo $v['id']; ?>, '<?php echo $v['is_active']? 'disable':'enable'; ?>')"><i class="fas <?php echo $v['is_active']? 'fa-toggle-off':'fa-toggle-on'; ?>"></i></button>
                                    <button type="button" class="btn-icon btn-link" title="Sao chép link đăng ký và tạo QR" onclick="VoucherPage.copyVoucherLinkAndShowQR('<?php echo IMAGE_HOST_BASE_URL . 'public/pages/auth/register.php?voucher=' . htmlspecialchars($v['code']); ?>', '<?php echo htmlspecialchars($v['code']); ?>')"><i class="fas fa-qrcode"></i></button>
                                    <button type="button" class="btn-icon btn-danger" title="Xóa" onclick="VoucherPage.deleteVoucher(<?php echo $v['id']; ?>)"><i class="fas fa-trash-alt"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="16">Không có voucher phù hợp.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php include $private_layouts_path . 'pagination.php'; ?>
    </div> <!-- End #voucher-management -->

</main>

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator/qrcode.js"></script>
<script>
    // Ensure appConfig is defined for JS
    window.appConfig = { basePath: '' };
</script>
<script src="<?php echo $base_url; ?>public/assets/js/utils/bulk_actions.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/voucher/voucher_management.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
