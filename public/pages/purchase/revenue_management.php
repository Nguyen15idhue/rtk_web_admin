<?php
// Replace manual session and path setup with bootstrap
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db               = $bootstrap_data['db'];
$base_url         = $bootstrap_data['base_url'];
$user_display_name= $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$private_actions_path = $bootstrap_data['private_actions_path'];

// Redirect nếu chưa auth
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$page_title = 'Quản lý Doanh thu'; // Define page title

require_once $private_actions_path . 'invoice/fetch_transactions.php';
require_once $private_actions_path . 'invoice/get_revenue_sums.php';

// Lấy params phân trang & filter
$current_page  = max(1, (int)($_GET['page'] ?? 1));
$per_page      = DEFAULT_ITEMS_PER_PAGE;
$filters = [
    'search'    => trim($_GET['search'] ?? ''),
    'status'    => trim($_GET['status'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to'   => trim($_GET['date_to'] ?? ''),
];

// Lấy dữ liệu giao dịch & phân trang
$data = fetch_admin_transactions($filters, $current_page, $per_page);
$transactions = $data['transactions'];
$total_items  = $data['total_count'];
$total_pages  = $data['total_pages'];
$current_page = $data['current_page'];
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');

// Get total and successful revenue using private action
list($total_revenue, $successful_revenue) = get_revenue_sums($filters);

// admin_header.php is assumed to open <html>, <head>, and <body>
include $private_layouts_path . 'admin_header.php'; 
include $private_layouts_path . 'admin_sidebar.php'; 
?>
    <main class="content-wrapper">
        <?php include $private_layouts_path . 'content_header.php'; ?>

        <div class="content-section">
            <h3>Doanh thu</h3>
            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" placeholder="Từ ngày">
                    <input type="date" name="date_to"   value="<?php echo htmlspecialchars($filters['date_to']); ?>"   placeholder="Đến ngày">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </form>

            <div class="stats-container">
                <div class="stats-box">
                    <i class="fas fa-coins icon"></i>
                    <div>
                        <span class="label">Tổng doanh thu</span>
                        <span class="value"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
                <div class="stats-box">
                    <i class="fas fa-check-circle icon"></i>
                    <div>
                        <span class="label">Tổng doanh thu thành công</span>
                        <span class="value"><?php echo number_format($successful_revenue, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
            </div>

            <!-- Thêm form xuất Excel -->
            <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
                <input type="hidden" name="table_name" value="transactions">
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
                                <th>Mã GD</th>
                                <th>Email</th>
                                <th>Gói</th>
                                <th>Số tiền</th>
                                <th>Ngày YC</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="7" style="text-align:center;">Không có giao dịch.</td></tr>
                            <?php else: foreach ($transactions as $tx): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="rowCheckbox" name="ids[]" 
                                               value="<?php echo htmlspecialchars($tx['registration_id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($tx['registration_id']); ?></td>
                                    <td><?php echo htmlspecialchars($tx['user_email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($tx['package_name']); ?></td>
                                    <td><?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($tx['request_date'])); ?></td>
                                    <td><?php echo get_status_badge('transaction', $tx['registration_status']); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>  <!-- End bulkActionForm -->

            <?php include $private_layouts_path . 'pagination.php'; ?>

        </div>
    </main>

    <!-- Đưa baseUrl vào JS và load file js mới -->
    <script>
        window.appConfig = {
            baseUrl: '<?php echo rtrim($base_url, '/'); ?>'
        };
    </script>
    <script src="<?php echo $base_url; ?>public/assets/js/pages/purchase/revenue_management.js"></script>

<?php 
// admin_footer.php is assumed to close </body> and </html>
include $private_layouts_path . 'admin_footer.php'; 
?>