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
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/actions/voucher/fetch_vouchers.php';

// --- Get Filters and Pagination Setup ---
$filters = [
    'q'     => (string)(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'type'  => (string)(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
    'status'=> (string)(filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
];
$items_per_page = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

// --- Fetch Vouchers ---
$data = fetch_paginated_vouchers($filters, $page, $items_per_page);
$vouchers = $data['vouchers'];
$total_items = $data['total_count'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];

// --- Build Pagination URL ---
$pagination_params = $filters;
unset($pagination_params['page']);
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
if (!empty($pagination_params)) {
    $pagination_base_url .= '?' . http_build_query($pagination_params) . '&';
} else {
    $pagination_base_url .= '?';
}

// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Voucher';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>

    <div id="voucher-management" class="content-section">
        <div class="header-actions">
            <h3>Quản lý Voucher</h3>
            <?php if ($admin_role === 'admin'): ?>
            <button class="btn btn-primary" onclick="VoucherPage.openCreateModal()"><i class="fas fa-plus"></i> Thêm Voucher</button>
            <?php endif; ?>
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
            </div>
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
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
                                <td><?php echo format_date($v['start_date']); ?> - <?php echo format_date($v['end_date']); ?></td>
                                <td><?php echo get_status_badge('voucher', $v['is_active'] ? 'active' : 'inactive'); ?></td>
                                <td>
                                    <button type="button" class="btn-icon btn-view" onclick="VoucherPage.viewDetails(<?php echo $v['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <?php if ($admin_role==='admin'): ?>
                                    <button type="button" class="btn-icon btn-edit" onclick="VoucherPage.openEditModal(<?php echo $v['id']; ?>)"><i class="fas fa-pencil-alt"></i></button>
                                    <button type="button" class="btn-icon" onclick="VoucherPage.toggleStatus(<?php echo $v['id']; ?>, '<?php echo $v['is_active']? 'disable':'enable'; ?>')"><i class="fas <?php echo $v['is_active']? 'fa-toggle-off':'fa-toggle-on'; ?>"></i></button>
                                    <button type="button" class="btn-icon btn-danger" onclick="VoucherPage.deleteVoucher(<?php echo $v['id']; ?>)"><i class="fas fa-trash-alt"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="13">Không có voucher phù hợp.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <div class="pagination-footer">
            <div class="pagination-info">
                <?php if ($total_items>0):
                    $start = ($current_page-1)*$items_per_page+1;
                    $end   = min($start+$items_per_page-1, $total_items);
                ?>
                Hiển thị <?php echo $start; ?>-<?php echo $end; ?> của <?php echo $total_items; ?> voucher
                <?php else: ?>
                Không có voucher nào
                <?php endif; ?>
            </div>
            <?php if ($total_pages>1): ?>
            <div class="pagination-controls">
                <button onclick="window.location.href='<?php echo $pagination_base_url.'page='.($current_page-1); ?>'" <?php echo $current_page<=1?'disabled':''; ?>>Tr</button>
                <?php for ($i=1;$i<=$total_pages;$i++): ?>
                <button class="<?php echo $i==$current_page?'active':''; ?>" onclick="window.location.href='<?php echo $pagination_base_url.'page='.$i; ?>'"><?php echo $i; ?></button>
                <?php endfor; ?>
                <button onclick="window.location.href='<?php echo $pagination_base_url.'page='.($current_page+1); ?>'" <?php echo $current_page>=$total_pages?'disabled':''; ?>>Sau</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Include modals and JS -->
<?php include $private_layouts_path . 'voucher_modals.php'; ?>
<script>
    // Ensure appConfig is defined for JS
    window.appConfig = { basePath: '' };
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/voucher/voucher_management.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
