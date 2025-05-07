<?php
// --- Bootstrap and Initialization ---
$bootstrap_data        = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$db                     = $bootstrap_data['db'];
$base_url               = $bootstrap_data['base_url'];
$private_includes_path  = $bootstrap_data['private_includes_path'];
$user_display_name      = $bootstrap_data['user_display_name'];

// authorization check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

require_once BASE_PATH . '/actions/invoice/fetch_invoices.php';
define('PDF_BASE_URL', $base_url . 'public/uploads/invoice/');

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 10;
$filters = ['status' => trim($_GET['status'] ?? '')];

$data = fetch_admin_invoices($filters, $current_page, $items_per_page);
$invoices = $data['invoices'];
$total = $data['total_count'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];

// --- add page bootstrap & title ---
$page_title = 'Phê duyệt Hóa đơn';
include $private_includes_path . 'admin_header.php';
include $private_includes_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo $user_display_name; ?></span>!</span>
            <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Yêu cầu Xuất Hóa đơn</h3>
        </div>
        <form method="GET" class="filter-bar">
            <select name="status">
                <option value="" <?php echo $filters['status']==''?'selected':''; ?>>Tất cả trạng thái</option>
                <option value="pending" <?php echo $filters['status']=='pending'?'selected':''; ?>>Chờ duyệt</option>
                <option value="approved" <?php echo $filters['status']=='approved'?'selected':''; ?>>Đã duyệt</option>
                <option value="rejected" <?php echo $filters['status']=='rejected'?'selected':''; ?>>Từ chối</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>GD#</th>
                        <th>Email</th>
                        <th>Gói</th>
                        <th>Yêu cầu</th>
                        <th class="status">Trạng thái</th>
                        <th>File</th>
                        <th>Lý do</th>
                        <th class="actions" style="text-align:center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                    <tr data-id="<?php echo $inv['invoice_id']; ?>">
                        <td><?php echo $inv['invoice_id']; ?></td>
                        <td><?php echo $inv['registration_id']; ?></td>
                        <td><?php echo htmlspecialchars($inv['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($inv['package_name']); ?></td>
                        <td><?php echo $inv['request_date']; ?></td>
                        <td class="status">
                            <span class="status-badge status-<?php echo $inv['status']; ?>">
                                <?php echo ucfirst($inv['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($inv['invoice_file']): ?>
                                <a href="<?php echo PDF_BASE_URL . $inv['invoice_file']; ?>" target="_blank">Xem PDF</a>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($inv['rejected_reason'] ?? ''); ?></td>
                        <td class="actions" style="text-align:center;">
                            <?php if ($inv['status'] === 'pending'): ?>
                                <a href="invoice_upload.php?invoice_id=<?php echo $inv['invoice_id']; ?>">
                                    <button type="button" class="btn-icon btn-approve" title="Upload & Approve">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </a>
                                <button class="btn-icon btn-reject" onclick="InvoiceReviewPageEvents.rejectInvoice(<?php echo $inv['invoice_id']; ?>)" title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php elseif ($inv['status'] === 'approved'): ?>
                                <span>Đã duyệt</span>
                            <?php else: ?>
                                <span>Đã từ chối</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    window.basePath = '<?php echo rtrim($base_url,'/'); ?>';
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/invoice_requests/invoice_review.js"></script>
<?php include $private_includes_path . 'admin_footer.php'; ?>