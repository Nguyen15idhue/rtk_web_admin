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
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách yêu cầu hỗ trợ</h3>
        </div>
        <?php
            // Server-side pagination setup
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = 10;
            $current_page = $page;
            $items_per_page = $perPage;
            $filters = [
                'search'   => trim($_GET['search'] ?? ''),
                'category' => trim($_GET['category'] ?? ''),
                'status'   => trim($_GET['status'] ?? ''),
            ];
            require_once __DIR__ . '/../../../private/classes/SupportRequestModel.php';
            $model = new SupportRequestModel();
            $total_items = $model->getCount($filters);
            $total_pages = (int) ceil($total_items / $perPage);
            $data = $model->getPaginated($filters, $page, $perPage);
            $pagination_base_url = $base_url . 'public/pages/support/support_management.php';
        ?>
        <form id="filterForm" class="filter-bar" method="GET" action="">
            <input type="search" id="searchInput" name="search" value="<?php echo escape_html($filters['search']); ?>" placeholder="Tìm kiếm...">
            <select id="categoryFilter" name="category">
                <option value="">Tất cả thể loại</option>
                <option value="technical" <?php echo ($filters['category']==='technical')?'selected':''; ?>>Kỹ thuật</option>
                <option value="billing" <?php echo ($filters['category']==='billing')?'selected':''; ?>>Thanh toán</option>
                <option value="account" <?php echo ($filters['category']==='account')?'selected':''; ?>>Tài khoản</option>
                <option value="other" <?php echo ($filters['category']==='other')?'selected':''; ?>>Khác</option>
            </select>
            <select id="statusFilter" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" <?php echo ($filters['status']==='pending')?'selected':''; ?>>Chờ xử lý</option>
                <option value="in_progress" <?php echo ($filters['status']==='in_progress')?'selected':''; ?>>Đang xử lý</option>
                <option value="resolved" <?php echo ($filters['status']==='resolved')?'selected':''; ?>>Đã giải quyết</option>
                <option value="closed" <?php echo ($filters['status']==='closed')?'selected':''; ?>>Đã đóng</option>
            </select>
            <button type="submit" class="btn btn-primary">Lọc</button>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Tiêu đề</th>
                            <th>Thể loại</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th class="actions text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_items > 0): ?>
                            <?php foreach ($data as $r): ?>
                                <tr data-id="<?php echo $r['id']; ?>">
                                    <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo $r['id']; ?>"></td>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo escape_html($r['user_email']); ?></td>
                                    <td><?php echo escape_html($r['subject']); ?></td>
                                    <td><?php echo (
                                        $r['category'] === 'technical' ? 'Kỹ thuật' : (
                                        $r['category'] === 'billing'   ? 'Thanh toán' : (
                                        $r['category'] === 'account'   ? 'Tài khoản'   : 'Khác'
                                        )
                                    )); ?></td>
                                    <td><?php echo get_status_badge('support', $r['status']); ?></td>
                                    <td><?php echo format_datetime($r['created_at']); ?></td>
                                    <td><?php echo $r['updated_at'] ? format_datetime($r['updated_at']) : ''; ?></td>
                                    <td class="actions text-center"><a href="<?php echo $base_url; ?>public/pages/support/support_edit.php?id=<?php echo $r['id']; ?>" class="btn-icon btn-edit" title="Sửa"><i class="fas fa-edit"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9">Không tìm thấy mục nào</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
        <!-- Pagination -->
        <?php include $private_layouts_path . 'pagination.php'; ?>
    </div>
</main>

<script>
    document.getElementById('selectAll').addEventListener('change', function(event) {
        document.querySelectorAll('.rowCheckbox').forEach(function(cb) {
            cb.checked = event.target.checked;
        });
    });
</script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
