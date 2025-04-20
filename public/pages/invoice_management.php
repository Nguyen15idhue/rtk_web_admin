<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\invoice_management.php
// --- Includes and Setup ---
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
$project_folder_index = array_search('rtk_web_admin', $script_name_parts);
$base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
$base_path = $protocol . $host . $base_path_segment;

require_once __DIR__ . '/../../private/config/database.php';
require_once __DIR__ . '/../../private/classes/Database.php';
require_once __DIR__ . '/../../private/utils/functions.php';
require_once __DIR__ . '/../../private/actions/invoice/fetch_transactions.php';

// --- Define the correct base URL for the image host ---
// !!! IMPORTANT: Replace this with the actual URL where your images are hosted !!!
define('IMAGE_HOST_BASE_URL', 'http://localhost:8000/'); // Example URL

$user_display_name = $_SESSION['admin_name'] ?? 'Admin';

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 15;
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'status' => trim($_GET['status'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
];

$transaction_data = fetch_admin_transactions($filters, $current_page, $items_per_page);
$transactions = $transaction_data['transactions'];
$total_items = $transaction_data['total_count'];
$total_pages = $transaction_data['total_pages'];
$current_page = $transaction_data['current_page'];

$pagination_base_url = '?' . http_build_query(array_filter($filters));

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Giao dịch - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-500: #3b82f6; --primary-600: #2563eb; --primary-700: #1d4ed8;
            --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb; --gray-300: #d1d5db;
            --gray-400: #9ca3af; --gray-500: #6b7280; --gray-600: #4b5563; --gray-700: #374151;
            --gray-800: #1f2937; --gray-900: #111827;
            --success-500: #10b981; --success-600: #059669;
            --danger-500: #ef4444; --danger-600: #dc2626;
            --warning-500: #f59e0b;
            --info-600: #0ea5e9;
            --badge-green-bg: #ecfdf5; --badge-green-text: #065f46;
            --badge-red-bg: #fef2f2; --badge-red-text: #991b1b;
            --badge-yellow-bg: #fffbeb; --badge-yellow-text: #b45309; --badge-yellow-border: #fde68a;
            --rounded-md: 0.375rem; --rounded-lg: 0.5rem; --rounded-full: 9999px;
            --font-size-xs: 0.75rem; --font-size-sm: 0.875rem; --font-size-base: 1rem; --font-size-lg: 1.125rem;
            --font-medium: 500; --font-semibold: 600;
            --border-color: var(--gray-200);
        }
        body { font-family: sans-serif; background-color: var(--gray-100); color: var(--gray-800); }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .content-wrapper { flex-grow: 1; padding: 1.5rem; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem 1.5rem; background: white; border-radius: var(--rounded-lg); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-header h2 { font-size: 1.5rem; font-weight: var(--font-semibold); color: var(--gray-800); }
        .user-info { display: flex; align-items: center; gap: 1rem; font-size: var(--font-size-sm); }
        .user-info span .highlight { color: var(--primary-600); font-weight: var(--font-semibold); }
        .user-info a { color: var(--primary-600); text-decoration: none; }
        .user-info a:hover { text-decoration: underline; }
        .content-section { background: white; border-radius: var(--rounded-lg); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-section h3 { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--gray-700); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; }
        .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
        .filter-bar input, .filter-bar select { padding: 0.6rem 0.8rem; border: 1px solid var(--gray-300); border-radius: var(--rounded-md); font-size: var(--font-size-sm); }
        .filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .filter-bar button, .filter-bar a.btn-secondary { padding: 0.6rem 1rem; font-size: var(--font-size-sm); }
        .transactions-table-wrapper { overflow-x: auto; background: white; border-radius: var(--rounded-lg); border: 1px solid var(--border-color); box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-top: 1rem; }
        .transactions-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .transactions-table th, .transactions-table td { padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); font-size: var(--font-size-sm); vertical-align: middle; }
        .transactions-table th { background-color: var(--gray-50); font-weight: var(--font-semibold); color: var(--gray-600); white-space: nowrap; }
        .transactions-table tr:last-child td { border-bottom: none; }
        .transactions-table tr:hover { background-color: var(--gray-50); }
        .transactions-table td.amount { font-weight: var(--font-medium); color: var(--gray-800); white-space: nowrap; }
        .transactions-table td.status { text-align: center; }
        .transactions-table td.actions { text-align: center; }
        .transactions-table td .action-buttons { display: inline-flex; gap: 0.5rem; justify-content: center; }
        .transactions-table td .btn-icon { background: none; border: none; cursor: pointer; padding: 0.3rem; font-size: 1.2rem; line-height: 1; }
        .transactions-table td .btn-approve { color: var(--success-600); }
        .transactions-table td .btn-reject { color: var(--danger-600); }
        .transactions-table td .btn-view-proof { color: var(--info-600); }
        .transactions-table td .btn-revert { color: var(--warning-500); }
        .transactions-table td .btn-disabled { color: var(--gray-400); cursor: not-allowed; }
        .transactions-table td .clickable-id { cursor: pointer; color: var(--primary-600); font-weight: var(--font-semibold); text-decoration: none; }
        .transactions-table td .clickable-id:hover { text-decoration: underline; }
        .status-badge { padding: 0.3rem 0.8rem; border-radius: var(--rounded-full); font-size: 0.8rem; display: inline-block; font-weight: var(--font-medium); text-align: center; min-width: 90px; border: 1px solid transparent; }
        .status-approved { background: var(--badge-green-bg); color: var(--badge-green-text); border-color: #a7f3d0; }
        .status-pending { background: var(--badge-yellow-bg); color: var(--badge-yellow-text); border-color: var(--badge-yellow-border); }
        .status-rejected { background: var(--badge-red-bg); color: var(--badge-red-text); border-color: #fecaca; }
        .status-unknown { background: var(--gray-100); color: var(--gray-500); border-color: var(--gray-300); }
        .pagination-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: var(--font-size-sm); color: var(--gray-600); }
        .pagination-controls { display: flex; gap: 0.3rem; }
        .pagination-controls button { padding: 0.4rem 0.8rem; border: 1px solid var(--gray-300); background-color: #fff; cursor: pointer; border-radius: var(--rounded-md); font-size: var(--font-size-sm); }
        .pagination-controls button:disabled { background-color: var(--gray-100); color: var(--gray-400); cursor: not-allowed; }
        .pagination-controls button.active { background-color: var(--primary-500); color: #fff; border-color: var(--primary-500); font-weight: bold; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content { background: white; padding: 1.5rem 2rem; border-radius: var(--rounded-lg); box-shadow: 0 5px 15px rgba(0,0,0,0.2); width: 90%; max-width: 600px; position: relative; transform: scale(0.9); transition: transform 0.3s ease; }
        .modal-overlay.active .modal-content { transform: scale(1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--gray-200); padding-bottom: 0.8rem; margin-bottom: 1rem; }
        .modal-header h4 { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--gray-800); }
        .modal-close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gray-500); padding: 0.2rem; line-height: 1; }
        .modal-close-btn:hover { color: var(--gray-700); }
        .modal-body p { margin-bottom: 0.75rem; font-size: var(--font-size-sm); color: var(--gray-700); line-height: 1.6; display: flex; }
        .modal-body strong { font-weight: var(--font-semibold); color: var(--gray-900); min-width: 140px; display: inline-block; margin-right: 0.5rem; }
        .modal-body span { flex-grow: 1; }
        .modal-body .status-badge-modal { margin-left: 5px; vertical-align: middle; }
        #proofModalImage { max-width: 100%; height: auto; display: block; margin: 1rem auto; border: 1px solid var(--gray-300); border-radius: var(--rounded-md); }
        #no-results-row td { text-align: center; padding: 3rem; color: var(--gray-500); font-size: var(--font-size-base); }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../../private/includes/admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Quản lý Giao dịch</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-invoice-management" class="content-section">
            <h3>Quản lý Giao dịch & Duyệt Thanh Toán</h3>

            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="search" placeholder="Tìm Mã GD, Email, Tên gói..." id="searchInput" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <select id="statusFilter" name="status">
                        <option value="" <?php echo ($filters['status'] == '') ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                        <option value="pending" <?php echo ($filters['status'] == 'pending') ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="approved" <?php echo ($filters['status'] == 'approved') ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="rejected" <?php echo ($filters['status'] == 'rejected') ? 'selected' : ''; ?>>Bị từ chối</option>
                    </select>
                    <input type="date" id="dateFrom" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" title="Từ ngày">
                    <input type="date" id="dateTo" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>" title="Đến ngày">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </form>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Email</th>
                            <th>Gói</th>
                            <th>Số tiền</th>
                            <th>Ngày YC</th>
                            <th style="text-align: center;">Xem MC</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th class="actions" style="text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr id="no-results-row">
                                <td colspan="8">Không tìm thấy giao dịch phù hợp.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <?php
                                    $status_info = get_admin_transaction_status_display($transaction['registration_status']);
                                    $transaction_id = $transaction['registration_id'];
                                    $proof_image_url = !empty($transaction['payment_image'])
                                        ? IMAGE_HOST_BASE_URL . 'public/uploads/payment_proofs/' . htmlspecialchars($transaction['payment_image'])
                                        : '';
                                    $is_pending = $transaction['registration_status'] === 'pending';
                                    $is_approved = $transaction['registration_status'] === 'active';
                                    $is_rejected = $transaction['registration_status'] === 'rejected';

                                    $tx_details_for_modal = [
                                        'id' => $transaction_id,
                                        'email' => $transaction['user_email'],
                                        'package_name' => $transaction['package_name'],
                                        'amount' => format_currency($transaction['amount']),
                                        'request_date' => format_datetime($transaction['request_date']),
                                        'status_text' => $status_info['text'],
                                        'status_class' => $status_info['class'],
                                        'proof_image' => $proof_image_url,
                                        'rejection_reason' => $transaction['rejection_reason'] ?? null
                                    ];
                                    $tx_details_json = htmlspecialchars(json_encode($tx_details_for_modal), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr data-transaction-id="<?php echo $transaction_id; ?>" data-status="<?php echo htmlspecialchars($transaction['registration_status']); ?>">
                                    <td>
                                        <a href="#" class="clickable-id" title="Xem chi tiết" onclick='showTransactionDetails(<?php echo $tx_details_json; ?>); return false;'>
                                            <?php echo $transaction_id; ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($transaction['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['package_name']); ?></td>
                                    <td class="amount"><?php echo format_currency($transaction['amount']); ?></td>
                                    <td><?php echo format_datetime($transaction['request_date']); ?></td>
                                    <td style="text-align: center;">
                                        <?php if ($proof_image_url): ?>
                                            <button class="btn-icon btn-view-proof" title="Xem Minh Chứng" onclick="viewProofModal('<?php echo $transaction_id; ?>', '<?php echo $proof_image_url; ?>')">
                                                <i class="fas fa-receipt"></i>
                                            </button>
                                        <?php else: ?>
                                            <span title="Không có minh chứng">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="status"><span class="status-badge <?php echo $status_info['class']; ?>"><?php echo $status_info['text']; ?></span></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <?php if ($is_pending): ?>
                                                <button class="btn-icon btn-approve" title="Duyệt" onclick="approveTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-reject" title="Từ chối" onclick="openRejectTransactionModal('<?php echo $transaction_id; ?>')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Chờ duyệt" disabled><i class="fas fa-undo-alt"></i></button>
                                            <?php elseif ($is_approved): ?>
                                                <button class="btn-icon btn-disabled" title="Đã duyệt" disabled><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-reject" title="Từ chối" onclick="openRejectTransactionModal('<?php echo $transaction_id; ?>')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-revert" title="Hủy duyệt (Về chờ duyệt)" onclick="revertTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_revert"><i class="fas fa-undo-alt"></i></button>
                                            <?php elseif ($is_rejected): ?>
                                                <button class="btn-icon btn-approve" title="Duyệt lại" onclick="approveTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Đã từ chối" disabled><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Đã từ chối" disabled><i class="fas fa-undo-alt"></i></button>
                                            <?php else: ?>
                                                <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-undo-alt"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-footer">
                <div class="pagination-info">
                    <?php if ($total_items > 0):
                        $start_item = ($current_page - 1) * $items_per_page + 1;
                        $end_item = min($start_item + $items_per_page - 1, $total_items);
                    ?>
                        Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> GD
                    <?php else: ?>
                        Không có giao dịch nào
                    <?php endif; ?>
                </div>
                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <button onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                    <?php
                        $max_pages_to_show = 7;
                        $start_page = max(1, $current_page - floor($max_pages_to_show / 2));
                        $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
                        $start_page = max(1, $end_page - $max_pages_to_show + 1);

                        if ($start_page > 1) {
                            echo '<button onclick="window.location.href=\'' . $pagination_base_url . '&page=1\'">1</button>';
                            if ($start_page > 2) {
                                echo '<button disabled>...</button>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . $i; ?>'"><?php echo $i; ?></button>
                        <?php endfor;

                        if ($end_page < $total_pages) {
                             if ($end_page < $total_pages - 1) {
                                echo '<button disabled>...</button>';
                            }
                            echo '<button onclick="window.location.href=\'' . $pagination_base_url . '&page=' . $total_pages . '\'">' . $total_pages . '</button>';
                        }
                    ?>
                    <button onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
                </div>
                 <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div id="proofModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="proofModalTitle">Minh chứng Giao dịch</h4>
            <button class="modal-close-btn" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center;">
             <img id="proofModalImage" src="" alt="Minh chứng thanh toán">
        </div>
    </div>
</div>

<div id="transaction-details-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-title">Chi Tiết Giao Dịch</h4>
            <button class="modal-close-btn" onclick="closeDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p><strong>Mã Giao dịch:</strong> <span id="modal-tx-id"></span></p>
            <p><strong>Email Người dùng:</strong> <span id="modal-tx-email"></span></p>
            <p><strong>Gói đăng ký:</strong> <span id="modal-tx-package"></span></p>
            <p><strong>Số tiền:</strong> <span id="modal-tx-amount"></span></p>
            <p><strong>Ngày yêu cầu:</strong> <span id="modal-tx-request-date"></span></p>
            <p><strong>Trạng thái:</strong>
                <span id="modal-tx-status-badge" class="status-badge status-badge-modal">
                    <span id="modal-tx-status-text"></span>
                </span>
            </p>
            <p id="modal-tx-rejection-reason-container" style="display: none;"><strong>Lý do từ chối:</strong> <span id="modal-tx-rejection-reason" style="color: var(--danger-600);"></span></p>
            <p><strong>Minh chứng:</strong> <span id="modal-tx-proof-link"></span></p>
        </div>
    </div>
</div>

<script>
    const proofModal = document.getElementById('proofModal');
    const proofModalImage = document.getElementById('proofModalImage');
    const proofModalTitle = document.getElementById('proofModalTitle');

    function viewProofModal(transactionId, imageUrl) {
        if (!proofModal || !proofModalImage || !proofModalTitle) {
            console.error("Proof Modal elements not found!");
            alert("Lỗi: Không thể hiển thị minh chứng.");
            return;
        }
        if (!imageUrl) {
             alert("Không có hình ảnh minh chứng cho giao dịch này.");
             return;
        }
        proofModalTitle.textContent = `Minh chứng Giao dịch #${transactionId}`;
        proofModalImage.src = imageUrl;
        proofModal.classList.add('active');
    }

    function closeProofModal() {
         if (proofModal) {
            proofModal.classList.remove('active');
         }
    }

    const detailsModalOverlay = document.getElementById('transaction-details-modal');
    const modalTxId = document.getElementById('modal-tx-id');
    const modalTxEmail = document.getElementById('modal-tx-email');
    const modalTxPackage = document.getElementById('modal-tx-package');
    const modalTxAmount = document.getElementById('modal-tx-amount');
    const modalTxRequestDate = document.getElementById('modal-tx-request-date');
    const modalTxStatusBadge = document.getElementById('modal-tx-status-badge');
    const modalTxStatusText = document.getElementById('modal-tx-status-text');
    const modalTxProofLink = document.getElementById('modal-tx-proof-link');
    const modalTxRejectionContainer = document.getElementById('modal-tx-rejection-reason-container');
    const modalTxRejectionReason = document.getElementById('modal-tx-rejection-reason');
    const modalTitle = document.getElementById('modal-title');

    function showTransactionDetails(txData) {
        if (!detailsModalOverlay || !txData) return;

        modalTitle.textContent = `Chi Tiết Giao Dịch #${txData.id}`;
        modalTxId.textContent = txData.id;
        modalTxEmail.textContent = txData.email;
        modalTxPackage.textContent = txData.package_name;
        modalTxAmount.textContent = txData.amount;
        modalTxRequestDate.textContent = txData.request_date;
        modalTxStatusText.textContent = txData.status_text;
        modalTxStatusBadge.className = 'status-badge status-badge-modal ' + txData.status_class;

        if (txData.proof_image) {
            modalTxProofLink.innerHTML = `<a href="${txData.proof_image}" target="_blank" title="Mở hình ảnh trong tab mới">Xem hình ảnh</a> | <button class="btn-link" onclick="viewProofModal('${txData.id}', '${txData.proof_image}'); closeDetailsModal();">Xem trong modal</button>`;
        } else {
            modalTxProofLink.textContent = 'Không có';
        }

        if (txData.rejection_reason) {
            modalTxRejectionReason.textContent = txData.rejection_reason;
            modalTxRejectionContainer.style.display = 'flex';
        } else {
            modalTxRejectionContainer.style.display = 'none';
        }

        detailsModalOverlay.classList.add('active');
    }

    function closeDetailsModal() {
        if (detailsModalOverlay) {
            detailsModalOverlay.classList.remove('active');
        }
    }

    window.addEventListener('click', function(event) {
        if (event.target == proofModal) {
            closeProofModal();
        }
        if (event.target == detailsModalOverlay) {
            closeDetailsModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            if (proofModal && proofModal.classList.contains('active')) {
                closeProofModal();
            }
            if (detailsModalOverlay && detailsModalOverlay.classList.contains('active')) {
                closeDetailsModal();
            }
        }
    });

    const approveUrl = '<?php echo $base_path; ?>private/actions/purchase/process_transaction_approve.php';
    const rejectUrl = '<?php echo $base_path; ?>private/actions/purchase/process_transaction_reject.php';
    const revertUrl = '<?php echo $base_path; ?>private/actions/purchase/process_transaction_revert.php';

    async function approveTransaction(transactionId, buttonElement) {
        console.log(`Approving transaction ${transactionId}`);
        if (!confirm(`Bạn có chắc muốn duyệt giao dịch #${transactionId}?`)) {
            return;
        }

        const row = buttonElement.closest('tr');
        disableActionButtons(row);

        try {
            const response = await fetch(approveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ transaction_id: transactionId })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Duyệt thành công!');
                updateTableRowStatus(transactionId, 'active', 'Đã duyệt', 'status-approved');
            } else {
                alert('Duyệt thất bại: ' + (data.message || 'Lỗi không xác định. Kiểm tra console log.'));
                console.error('Approval Error:', data);
                enableActionButtons(row, 'rejected');
            }
        } catch (error) {
            console.error('Error approving transaction:', error);
            alert('Có lỗi xảy ra khi duyệt giao dịch. Kiểm tra console log.');
            enableActionButtons(row, 'rejected');
        }
    }

    async function openRejectTransactionModal(transactionId) {
        console.log(`Opening reject modal for transaction ${transactionId}`);
        const reason = prompt(`Nhập lý do từ chối cho giao dịch #${transactionId}:`);

        if (reason === null) return;

        const trimmedReason = reason.trim();
        if (trimmedReason === '') {
            alert('Vui lòng nhập lý do từ chối.');
            return;
        }

        const row = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
        const currentStatus = row.dataset.status;
        disableActionButtons(row);

        try {
            const response = await fetch(rejectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ transaction_id: transactionId, reason: trimmedReason })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Từ chối thành công!');
                updateTableRowStatus(transactionId, 'rejected', 'Bị từ chối', 'status-rejected');
            } else {
                alert('Từ chối thất bại: ' + (data.message || 'Lỗi không xác định. Kiểm tra console log.'));
                console.error('Rejection Error:', data);
                enableActionButtons(row, currentStatus);
            }
        } catch (error) {
            console.error('Error rejecting transaction:', error);
            alert('Có lỗi xảy ra khi từ chối giao dịch. Kiểm tra console log.');
            enableActionButtons(row, currentStatus);
        }
    }

    async function revertTransaction(transactionId, buttonElement) {
        console.log(`Reverting transaction ${transactionId}`);
        if (!confirm(`Bạn có chắc muốn hủy duyệt giao dịch #${transactionId} và đưa về trạng thái "Chờ duyệt"?`)) {
            return;
        }

        const row = buttonElement.closest('tr');
        disableActionButtons(row);

        try {
            const response = await fetch(revertUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ transaction_id: transactionId })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Hủy duyệt thành công! Giao dịch đã được đưa về trạng thái "Chờ duyệt".');
                updateTableRowStatus(transactionId, 'pending', 'Chờ duyệt', 'status-pending');
            } else {
                alert('Hủy duyệt thất bại: ' + (data.message || 'Lỗi không xác định. Kiểm tra console log.'));
                console.error('Revert Error:', data);
                enableActionButtons(row, 'active');
            }
        } catch (error) {
            console.error('Error reverting transaction:', error);
            alert('Có lỗi xảy ra khi hủy duyệt giao dịch. Kiểm tra console log.');
            enableActionButtons(row, 'active');
        }
    }

    function disableActionButtons(row) {
        if (!row) return;
        row.querySelectorAll('.action-buttons button').forEach(btn => btn.disabled = true);
    }

    function enableActionButtons(row, originalStatus) {
        if (!row) return;
        const approveBtn = row.querySelector('.btn-approve');
        const rejectBtn = row.querySelector('.btn-reject');
        const revertBtn = row.querySelector('.btn-revert');

        if (approveBtn) approveBtn.disabled = !(originalStatus === 'pending' || originalStatus === 'rejected');
        if (rejectBtn) rejectBtn.disabled = !(originalStatus === 'pending' || originalStatus === 'active');
        if (revertBtn) revertBtn.disabled = !(originalStatus === 'active');

        row.querySelectorAll('.btn-disabled').forEach(btn => btn.disabled = true);
    }

    function updateTableRowStatus(transactionId, newDbStatus, newStatusText, newStatusClass) {
        const row = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
        if (!row) return;

        row.dataset.status = newDbStatus;

        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${newStatusClass}`;
            statusBadge.textContent = newStatusText;
        }

        const actionButtonsCell = row.querySelector('.actions .action-buttons');
        if (actionButtonsCell) {
            let buttonsHtml = '';
            if (newDbStatus === 'pending') {
                buttonsHtml = `
                    <button class="btn-icon btn-approve" title="Duyệt" onclick="approveTransaction('${transactionId}', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
                    <button class="btn-icon btn-reject" title="Từ chối" onclick="openRejectTransactionModal('${transactionId}')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                    <button class="btn-icon btn-disabled" title="Chờ duyệt" disabled><i class="fas fa-undo-alt"></i></button>
                `;
            } else if (newDbStatus === 'active') {
                buttonsHtml = `
                    <button class="btn-icon btn-disabled" title="Đã duyệt" disabled><i class="fas fa-check-circle"></i></button>
                    <button class="btn-icon btn-reject" title="Từ chối" onclick="openRejectTransactionModal('${transactionId}')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                    <button class="btn-icon btn-revert" title="Hủy duyệt (Về chờ duyệt)" onclick="revertTransaction('${transactionId}', this)" data-permission="transaction_revert"><i class="fas fa-undo-alt"></i></button>
                `;
            } else if (newDbStatus === 'rejected') {
                 buttonsHtml = `
                    <button class="btn-icon btn-approve" title="Duyệt lại" onclick="approveTransaction('${transactionId}', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
                    <button class="btn-icon btn-disabled" title="Đã từ chối" disabled><i class="fas fa-times-circle"></i></button>
                    <button class="btn-icon btn-disabled" title="Đã từ chối" disabled><i class="fas fa-undo-alt"></i></button>
                `;
            } else {
                 buttonsHtml = `
                    <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-check-circle"></i></button>
                    <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-times-circle"></i></button>
                    <button class="btn-icon btn-disabled" title="Không xác định" disabled><i class="fas fa-undo-alt"></i></button>
                `;
            }
            actionButtonsCell.innerHTML = buttonsHtml;
        }
    }

    const styleSheet = document.createElement("style");
    styleSheet.innerText = `
        .btn-link {
            background: none;
            border: none;
            color: var(--primary-600);
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            font-size: inherit;
        }
        .btn-link:hover {
            color: var(--primary-700);
        }
    `;
    document.head.appendChild(styleSheet);

</script>

</body>
</html>
