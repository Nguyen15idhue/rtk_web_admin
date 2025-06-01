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
list($total_revenue, $successful_revenue, $pending_revenue, $rejected_revenue) = get_revenue_sums($filters);

// admin_header.php is assumed to open <html>, <head>, and <body>
include $private_layouts_path . 'admin_header.php'; 
include $private_layouts_path . 'admin_sidebar.php'; 
?>
    <main class="content-wrapper">
        <?php include $private_layouts_path . 'content_header.php'; ?>

        <div class="content-section">
            <h3>Doanh thu</h3>
            <!-- Thêm form xuất Excel với các tùy chọn nâng cao -->
            <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
                <input type="hidden" name="table_name" value="transactions">
                <input type="hidden" name="filters" value="<?php echo htmlspecialchars(json_encode($filters)); ?>">
                <div class="bulk-actions-bar">
                    <button type="submit" name="export_selected" class="btn btn-info" disabled>
                        <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                    </button>
                    <button type="submit" name="export_all" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Xuất tất cả (<?php echo $total_items; ?> mục)
                    </button>
                    <button type="button" class="btn btn-warning" onclick="exportRevenueSummary()">
                        <i class="fas fa-chart-bar"></i> Xuất báo cáo tổng hợp
                    </button>
                </div>
            </form>
            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" placeholder="Từ ngày" title="Từ ngày">
                    <input type="date" name="date_to"   value="<?php echo htmlspecialchars($filters['date_to']); ?>"   placeholder="Đến ngày" title="Đến ngày">
                    
                    <select name="status" title="Lọc theo trạng thái">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="approved" <?php echo $filters['status'] === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="rejected" <?php echo $filters['status'] === 'rejected' ? 'selected' : ''; ?>>Bị từ chối</option>
                    </select>
                    
                    <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" placeholder="Tìm theo mã GD hoặc email" title="Tìm kiếm">
                    
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
                        <span class="label">Doanh thu thành công</span>
                        <span class="value"><?php echo number_format($successful_revenue, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
                <div class="stats-box">
                    <i class="fas fa-percentage icon"></i>
                    <div>
                        <span class="label">Tỷ lệ thành công</span>
                        <span class="value">
                            <?php 
                            $success_rate = $total_revenue > 0 ? ($successful_revenue / $total_revenue * 100) : 0;
                            echo number_format($success_rate, 1) . '%'; 
                            ?>
                        </span>
                    </div>
                </div>
                <div class="stats-box">
                    <i class="fas fa-list-ol icon"></i>
                    <div>
                        <span class="label">Tổng giao dịch</span>
                        <span class="value"><?php echo number_format($total_items, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
                <div class="table-wrapper">
                    <table class="table">                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Mã GD</th>
                                <th>Loại</th>
                                <th>Email</th>
                                <th>Gói</th>
                                <th>Số tiền</th>
                                <th>Ngày YC</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead><tbody>                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="8" style="text-align:center;">Không có giao dịch.</td></tr>
                            <?php else: foreach ($transactions as $tx): ?><?php 
                                $typeText = $tx['transaction_type'] === 'renewal' ? 'Gia hạn' : 'Đăng ký mới';
                                $transactionId = htmlspecialchars($tx['registration_id']);
                                ?>
                                <tr data-transaction-id="<?php echo $transactionId; ?>" data-type="<?php echo htmlspecialchars($tx['transaction_type']); ?>" data-status="<?php echo htmlspecialchars($tx['registration_status']); ?>">
                                    <td>
                                        <input type="checkbox" class="rowCheckbox" name="ids[]" 
                                               value="<?php echo $transactionId; ?>">
                                    </td>                                    <td>
                                        <?php echo $transactionId; ?>
                                    </td>
                                    <td><span class="transaction-type <?php echo $tx['transaction_type']; ?>"><?php echo $typeText; ?></span></td>
                                    <td><?php echo htmlspecialchars($tx['user_email'] ?? ''); ?></td>                                    <td><?php echo htmlspecialchars($tx['package_name']); ?></td>
                                    <td class="amount"><?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($tx['request_date'])); ?></td>
                                    <td><?php echo get_status_badge('transaction', $tx['registration_status']); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>            </form>  <!-- End bulkActionForm -->

            <!-- Hidden form for revenue summary export -->
            <form id="revenueSummaryForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php" style="display: none;">
                <input type="hidden" name="action" value="export_revenue_summary">
                <input type="hidden" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                <input type="hidden" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($filters['status']); ?>">
            </form>

            <?php include $private_layouts_path . 'pagination.php'; ?></div>    </main>

    <!-- Đưa baseUrl vào JS và load file js mới -->
    <script>
        window.appConfig = {
            baseUrl: '<?php echo rtrim($base_url, '/'); ?>'
        };
    </script>
    <script src="<?php echo $base_url; ?>public/assets/js/pages/purchase/revenue_management.js"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/pages/purchase/revenue_management.css">

<?php 
// admin_footer.php is assumed to close </body> and </html>
include $private_layouts_path . 'admin_footer.php'; 
?>