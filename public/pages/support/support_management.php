<?php
// filepath: public/pages/support/support_management.php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$admin_role = $bootstrap_data['admin_role'];

$page_title = 'Chăm sóc khách hàng';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

require_once BASE_PATH . '/classes/SupportRequestModel.php';
$model = new SupportRequestModel();

// --- Get Filters ---
$filters = [
    'search'    => trim($_GET['search'] ?? ''),
    'category'  => trim($_GET['category'] ?? ''),
    'status'    => trim($_GET['status'] ?? ''),
    'priority'  => trim($_GET['priority'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to'   => trim($_GET['date_to'] ?? ''),
];

// --- Pagination Setup ---
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Support Requests ---
$supports = $model->getPaginated($filters, $current_page, $items_per_page);
$total_items = $model->getCount($filters);
$total_pages = (int) ceil($total_items / $items_per_page);
$pagination_base_url = strtok($_SERVER['REQUEST_URI'], '?');
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách yêu cầu hỗ trợ</h3>
        </div>
        <form method="GET" class="filter-bar">
            <div class="filter-row" style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                <input type="search" name="search" id="searchInput" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                <select name="category" id="categoryFilter">
                    <option value="" <?php echo $filters['category']==''?'selected':''; ?>>Tất cả thể loại</option>
                    <option value="technical" <?php echo $filters['category']=='technical'?'selected':''; ?>>Kỹ thuật</option>
                    <option value="billing" <?php echo $filters['category']=='billing'?'selected':''; ?>>Thanh toán</option>
                    <option value="account" <?php echo $filters['category']=='account'?'selected':''; ?>>Tài khoản</option>
                    <option value="other" <?php echo $filters['category']=='other'?'selected':''; ?>>Khác</option>
                </select>
                <select name="status" id="statusFilter">
                    <option value="" <?php echo $filters['status']==''?'selected':''; ?>>Tất cả trạng thái</option>
                    <option value="pending" <?php echo $filters['status']=='pending'?'selected':''; ?>>Chờ xử lý</option>
                    <option value="in_progress" <?php echo $filters['status']=='in_progress'?'selected':''; ?>>Đang xử lý</option>
                    <option value="resolved" <?php echo $filters['status']=='resolved'?'selected':''; ?>>Đã giải quyết</option>
                    <option value="closed" <?php echo $filters['status']=='closed'?'selected':''; ?>>Đã đóng</option>
                </select>
                <select name="priority" id="priorityFilter">
                    <option value="" <?php echo $filters['priority']==''?'selected':''; ?>>Tất cả mức độ ưu tiên</option>
                    <option value="urgent" <?php echo $filters['priority']=='urgent'?'selected':''; ?>>Khẩn cấp</option>
                    <option value="high" <?php echo $filters['priority']=='high'?'selected':''; ?>>Cao</option>
                    <option value="medium" <?php echo $filters['priority']=='medium'?'selected':''; ?>>Trung bình</option>
                    <option value="low" <?php echo $filters['priority']=='low'?'selected':''; ?>>Thấp</option>
                </select>
                <input type="date" name="date_from" id="dateFrom" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                <input type="date" name="date_to" id="dateTo" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                <div class="filter-actions" style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                    <a href="<?php echo $pagination_base_url; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </div>
        </form>

        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="support_requests">
            <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px;">
                <button type="submit" name="export_selected" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                </button>
                <button type="submit" name="export_all" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất tất cả
                </button>
            </div>

            <div class="table-wrapper">
                <table class="table" id="tbl-support">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Tiêu đề</th>
                            <th>Thể loại</th>
                            <th>Mức độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th class="actions text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($supports)): ?>
                            <?php foreach ($supports as $r): ?>
                                <tr>
                                    <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo $r['id']; ?>"></td>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($r['subject']); ?></td>
                                    <td><?php echo ($r['category']=='technical'?'Kỹ thuật':($r['category']=='billing'?'Thanh toán':($r['category']=='account'?'Tài khoản':'Khác'))); ?></td>
                                    <td><?php echo get_status_badge('priority', $r['priority']); ?></td>
                                    <td><?php echo get_status_badge('support', $r['status']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($r['created_at'])); ?></td>
                                    <td><?php echo $r['updated_at']?date('Y-m-d H:i', strtotime($r['updated_at'])):'—'; ?></td>
                                    <td class="actions text-center">
                                        <button type="button" class="btn-icon btn-edit" data-id="<?php echo $r['id']; ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10">Không có yêu cầu hỗ trợ phù hợp.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php include $private_layouts_path . 'pagination.php'; ?>
    </div>
</main>

<script>
    window.basePath = '<?php echo rtrim($base_url, '/'); ?>';
</script>
<script defer src="<?php echo $base_url; ?>public/assets/js/pages/support/support_management.js"></script>

<?php include $private_layouts_path . 'admin_footer.php'; ?>