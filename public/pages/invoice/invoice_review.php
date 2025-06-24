<?php
$GLOBALS['required_permission'] = 'invoice_review'; // Added permission requirement

// --- Bootstrap and Initialization ---
$bootstrap_data        = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                     = $bootstrap_data['db'];
$base_url               = $bootstrap_data['base_url'];
$private_layouts_path  = $bootstrap_data['private_layouts_path'];
$user_display_name      = $bootstrap_data['user_display_name'];

// --- add page bootstrap & title ---
$page_title = 'Phê duyệt Hóa đơn';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

require_once BASE_PATH . '/actions/invoice/fetch_invoices.php';
require_once BASE_PATH . '/utils/invoice_review_helpers.php';
define('PDF_BASE_URL', $base_url . 'public/uploads/invoice/');

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$filters = [
    'status' => trim($_GET['status'] ?? ''),
    'email' => trim($_GET['email'] ?? ''),
    'company_name' => trim($_GET['company_name'] ?? ''),
    'tax_code' => trim($_GET['tax_code'] ?? ''),
    'invoice_id' => trim($_GET['invoice_id'] ?? '') // Thay đổi từ transaction_id sang invoice_id
];

$data = fetch_admin_invoices($filters, $current_page, $items_per_page);
$invoices = $data['invoices'];
$total = $data['total_count'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];

// --- Build Pagination URL ---
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');

$status_options = [
    '' => 'Tất cả trạng thái',
    'pending' => 'Chờ duyệt',
    'approved' => 'Đã duyệt',
    'rejected' => 'Từ chối'
];

$isEditInvoiceAllowed = Auth::can('invoice_review_edit');
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Yêu cầu Xuất Hóa đơn</h3>
        </div>        <form method="GET" class="filter-bar">            <div class="filter-row">
                <select name="status" class="form-select">
                    <?php echo buildSelectOptions($status_options, $filters['status']); ?>
                </select>
                
                <input type="text" name="email" class="form-input" placeholder="Email khách hàng" 
                       value="<?php echo htmlspecialchars($filters['email']); ?>">
                
                <input type="text" name="company_name" class="form-input" placeholder="Tên công ty" 
                       value="<?php echo htmlspecialchars($filters['company_name']); ?>">
                
                <input type="text" name="tax_code" class="form-input" placeholder="Mã số thuế" 
                       value="<?php echo htmlspecialchars($filters['tax_code']); ?>">
                
                <input type="text" name="invoice_id" class="form-input" placeholder="ID hóa đơn" 
                       value="<?php echo htmlspecialchars($filters['invoice_id']); ?>"> 
                
                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
        </form>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>GD#</th>
                        <th>Email</th>
                        <th>Tên công ty</th>
                        <th>Mã số thuế</th>
                        <th>Địa chỉ công ty</th>
                        <th>Gói</th>
                        <th>Yêu cầu</th>
                        <th class="status">Trạng thái</th>
                        <th>File</th>
                        <th>Lý do</th>
                        <th class="actions" style="text-align:center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>                    <tr data-id="<?php echo $inv['invoice_id']; ?>">
                        <td><?php echo $inv['invoice_id']; ?></td>
                        <td>
                            <a href="#" class="clickable-id" title="Xem chi tiết giao dịch" onclick="InvoiceReviewPageEvents.showTransactionDetails(<?php echo $inv['transaction_history_id']; ?>); return false;">
                                <?php echo $inv['transaction_history_id']; ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($inv['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($inv['company_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($inv['tax_code'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($inv['company_address'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($inv['package_name']); ?></td>
                        <td>
                            <?php 
                                // đổi định dạng ngày giờ sang dd-mm-yyyy hh:mm:ss
                                echo format_datetime($inv['request_date']); 
                            ?>
                        </td>
                        <td class="status">
                            <?php echo get_status_badge('invoice', $inv['status']); ?>
                        </td>                        <td>
                            <?php renderPDFViewer($inv, PDF_BASE_URL); ?>
                        </td>
                        <td>
                            <?php renderRejectionReason($inv); ?>
                        </td><td class="actions">
                            <?php renderInvoiceActions($inv, $isEditInvoiceAllowed); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php include $private_layouts_path . 'pagination.php'; ?>    </div>
</main>

<!-- PDF Modal Styles -->
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/pages/invoice/invoice_review.css">

<!-- Transaction Details Modal -->
<div id="transaction-details-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-title">Chi Tiết Giao Dịch</h4>
            <button class="modal-close" onclick="InvoiceReviewPageEvents.closeTransactionDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <span class="detail-label">Mã Giao dịch:</span>
                <span class="detail-value" id="modal-tx-id"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email Người dùng:</span>
                <span class="detail-value" id="modal-tx-email"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Gói đăng ký:</span>
                <span class="detail-value" id="modal-tx-package"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Voucher:</span>
                <span class="detail-value" id="modal-tx-voucher-code"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mô tả Voucher:</span>
                <span class="detail-value" id="modal-tx-voucher-description"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ngày bắt đầu:</span>
                <span class="detail-value" id="modal-tx-voucher-start-date"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ngày kết thúc:</span>
                <span class="detail-value" id="modal-tx-voucher-end-date"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Số tiền:</span>
                <span class="detail-value" id="modal-tx-amount"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ngày yêu cầu:</span>
                <span class="detail-value" id="modal-tx-request-date"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Trạng thái:</span>
                <span class="detail-value">
                    <span id="modal-tx-status-badge" class="status-badge status-badge-modal">
                        <span id="modal-tx-status-text"></span>
                    </span>
                </span>
            </div>
            <div class="detail-row" id="modal-tx-rejection-reason-container">
                <span class="detail-label">Lý do từ chối:</span>
                <span class="detail-value" id="modal-tx-rejection-reason" style="color: var(--danger-600);"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Minh chứng:</span>
                <span class="detail-value" id="modal-tx-proof-link"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="InvoiceReviewPageEvents.closeTransactionDetailsModal()">Đóng</button>
        </div>
    </div>
</div>

<script>
    // Configuration for Invoice Review Page
    window.appConfig = {
        baseUrl: '<?php echo rtrim($base_url,'/'); ?>',
        permissions: {
            invoice_review_edit: <?php echo json_encode($isEditInvoiceAllowed); ?>
        }
    };
</script>

<?php include $private_layouts_path . 'generic_modal.php'; ?>
<script src="<?php echo $base_url; ?>public/assets/js/pages/invoice/invoice_review_modal.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/invoice/invoice_review.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>