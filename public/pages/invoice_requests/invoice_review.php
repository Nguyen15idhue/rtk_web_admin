<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

require_once __DIR__ . '/../../../private/config/database.php';
require_once __DIR__ . '/../../../private/classes/Database.php';
require_once __DIR__ . '/../../../private/actions/invoice/fetch_invoices.php';

// --- Base Path Calculation for assets ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off' || $_SERVER['SERVER_PORT']==443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$idx = array_search('rtk_web_admin', $parts);
$base_path = $protocol . $host . ($idx !== false ? implode('/', array_slice($parts, 0, $idx+1)) . '/' : '/');

define('PDF_BASE_URL', $base_path . 'public/uploads/invoice/');

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 10;
$filters = ['status' => trim($_GET['status'] ?? '')];

$data = fetch_admin_invoices($filters, $current_page, $items_per_page);
$invoices = $data['invoices'];
$total = $data['total_count'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];
$private_includes_path = __DIR__ . '/../../../private/includes/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phê duyệt Hóa đơn</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .status-pending { color: #f59e0b; }
        .status-approved { color: #10b981; }
        .status-rejected { color: #ef4444; }
    </style>
</head>
<body>
<?php 
    include $private_includes_path . 'admin_header.php';
    include $private_includes_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <div class="content-section">
        <h2>Danh sách Yêu cầu Xuất Hóa đơn</h2>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>ID</th><th>GD#</th><th>Email</th><th>Gói</th><th>Yêu cầu</th><th>Trạng thái</th><th>File</th><th>Lý do</th>
                        <th style="text-align:center;">Thao tác</th>
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
                        <td class="status-<?php echo $inv['status']; ?>"><?php echo ucfirst($inv['status']); ?></td>
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
                                    <button type="button" title="Upload & Approve">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                </a>
                                <button onclick="rejectInvoice(<?php echo $inv['invoice_id']; ?>)" title="Từ chối">
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
<script src="<?php echo $base_path; ?>public/assets/js/pages/invoice_requests/invoice_review.js"></script>
</body>
</html>