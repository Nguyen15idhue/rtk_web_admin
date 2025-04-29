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

// --- LẤY DỮ LIỆU CHO FILTERS MỚI ---
$db = Database::getInstance()->getConnection();
$packages_stmt = $db->query("SELECT id, name FROM package WHERE is_active=1 ORDER BY display_order");
$packages = $packages_stmt->fetchAll(PDO::FETCH_ASSOC);
$provinces_stmt = $db->query("SELECT province FROM location WHERE status=1 ORDER BY province");
$provinces = $provinces_stmt->fetchAll(PDO::FETCH_COLUMN);

// --- Define the correct base URL for the image host ---
// !!! IMPORTANT: Replace this with the actual URL where your images are hosted !!!
define('IMAGE_HOST_BASE_URL', 'http://localhost:8000/'); // Example URL

$user_display_name = $_SESSION['admin_username'] ?? 'Admin';

$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 15;
$filters = [
    'search'    => trim($_GET['search']    ?? ''),
    'status'    => trim($_GET['status']    ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to'   => trim($_GET['date_to']   ?? ''),
    'package_id'=> trim($_GET['package_id']?? ''),
    'province'  => trim($_GET['province']  ?? ''),
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
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">
    <style>
        /* Modal styling */
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
    </style>
</head>
<body>


    <?php include __DIR__ . '/../../private/includes/admin_header.php'; ?>
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
                    <!-- 1. Date range first -->
                    <input type="date" id="dateFrom" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" title="Từ ngày">
                    <input type="date" id="dateTo" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>" title="Đến ngày">
                    <!-- 2. Status filter -->
                    <select id="statusFilter" name="status">
                        <option value="" <?php echo ($filters['status'] == '') ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                        <option value="pending" <?php echo ($filters['status'] == 'pending') ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="active" <?php echo ($filters['status'] == 'active') ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="rejected" <?php echo ($filters['status'] == 'rejected') ? 'selected' : ''; ?>>Bị từ chối</option>
                    </select>
                    <!-- 3. Package filter -->
                    <select id="packageFilter" name="package_id">
                        <option value="" <?php echo ($filters['package_id'] === '') ? 'selected' : ''; ?>>Tất cả Gói</option>
                        <?php foreach($packages as $pkg): ?>
                            <option value="<?php echo $pkg['id']; ?>" <?php echo ($filters['package_id'] == $pkg['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pkg['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- 4. Province filter -->
                    <select id="provinceFilter" name="province">
                        <option value="" <?php echo ($filters['province'] === '') ? 'selected' : ''; ?>>Tất cả Tỉnh</option>
                        <?php foreach($provinces as $prov): ?>
                            <option value="<?php echo htmlspecialchars($prov); ?>" <?php echo ($filters['province'] == $prov) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prov); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- 5. Search input last -->
                    <input type="search" placeholder="Tìm Mã GD, Email..." id="searchInput" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <!-- buttons -->
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;">
                        <i class="fas fa-times"></i> Xóa lọc
                    </a>
                </div>
            </form>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Tỉnh/Thành phố</th>
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
                                <td colspan="9">Không tìm thấy giao dịch phù hợp.</td>
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
                                    <td><?php echo htmlspecialchars($transaction['province'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['user_email']?? ''); ?></td>
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

<!-- cung cấp basePath cho JS -->
<script>
    window.appConfig = { basePath: '<?php echo $base_path; ?>' };
</script>
<script src="<?php echo $base_path; ?>public/assets/js/pages/invoice/invoice_management.js"></script>
</body>
</html>
