<?php
$GLOBALS['required_permission'] = 'invoice_review'; // Added permission requirement

// --- Bootstrap and Initialization ---
$bootstrap_data        = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                     = $bootstrap_data['db'];
$base_url               = $bootstrap_data['base_url'];
$private_layouts_path  = $bootstrap_data['private_layouts_path'];
$user_display_name      = $bootstrap_data['user_display_name'];

require_once BASE_PATH . '/actions/invoice/fetch_invoices.php';
define('PDF_BASE_URL', $base_url . 'public/uploads/invoice/');

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$filters = ['status' => trim($_GET['status'] ?? '')];

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

// --- add page bootstrap & title ---
$page_title = 'Phê duyệt Hóa đơn';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

$isEditInvoiceAllowed = Auth::can('invoice_review_edit');
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Yêu cầu Xuất Hóa đơn</h3>
        </div>
        <form method="GET" class="filter-bar">
            <select name="status">
                <?php foreach ($status_options as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo ($filters['status'] === $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>
        <div class="table-wrapper">
            <table class="table">
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
                        <td><?php echo $inv['transaction_history_id']; ?></td>
                        <td><?php echo htmlspecialchars($inv['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($inv['package_name']); ?></td>
                        <td>
                            <?php 
                                // đổi định dạng ngày giờ sang dd-mm-yyyy hh:mm:ss
                                echo format_datetime($inv['request_date']); 
                            ?>
                        </td>
                        <td class="status">
                            <?php echo get_status_badge('invoice', $inv['status']); ?>
                        </td>
                        <td>
                            <?php if ($inv['invoice_file']): ?>
                                <a href="<?php echo PDF_BASE_URL . $inv['invoice_file']; ?>" target="_blank">Xem PDF</a>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($inv['rejected_reason'] ?? ''); ?></td>
                        <td class="actions" style="text-align:center;">
                            <?php if ($inv['status'] === 'pending' && $isEditInvoiceAllowed): ?>
                                <a href="invoice_upload.php?invoice_id=<?php echo $inv['invoice_id']; ?>">
                                    <button type="button" class="btn-icon btn-approve" title="Upload & Approve">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </a>
                                <button class="btn-icon btn-reject" onclick="InvoiceReviewPageEvents.rejectInvoice(<?php echo $inv['invoice_id']; ?>)" title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php elseif ($inv['status'] === 'approved'): ?>
                                <?php if ($isEditInvoiceAllowed): ?>
                                <button class="btn-icon btn-undo" onclick="InvoiceReviewPageEvents.undoInvoice(<?php echo $inv['invoice_id']; ?>)" title="Hoàn tác">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <?php endif; ?>
                            <?php else: // rejected ?>
                                <?php if ($isEditInvoiceAllowed): ?>
                                <button class="btn-icon btn-undo" onclick="InvoiceReviewPageEvents.undoInvoice(<?php echo $inv['invoice_id']; ?>)" title="Hoàn tác">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php include $private_layouts_path . 'pagination.php'; ?>
    </div>
</main>
<script>
    window.appConfig = {
        baseUrl: '<?php echo rtrim($base_url,'/'); ?>',
        permissions: {
            invoice_review_edit: <?php echo json_encode($isEditInvoiceAllowed); ?>
        }
    };
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/invoice/invoice_review.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>