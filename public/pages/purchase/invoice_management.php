<?php
// filepath: public/pages/invoice/invoice_management.php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                    = $bootstrap_data['db'];
$base_url             = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$private_actions_path = $bootstrap_data['private_actions_path'];
$user_display_name     = $bootstrap_data['user_display_name'];

require_once $private_actions_path . 'invoice/fetch_transactions.php';

// --- LẤY DỮ LIỆU CHO FILTERS MỚI ---
$packages_stmt = $db->query("SELECT id, name FROM package WHERE is_active=1 ORDER BY display_order");
$packages = $packages_stmt->fetchAll(PDO::FETCH_ASSOC);
$provinces_stmt = $db->query("SELECT province FROM location WHERE status=1 ORDER BY province");
$provinces = $provinces_stmt->fetchAll(PDO::FETCH_COLUMN);

$current_page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$filters = [
    'search'     => trim($_GET['search']    ?? ''),
    'status'     => trim($_GET['status']    ?? ''),
    'date_from'  => trim($_GET['date_from'] ?? ''),
    'date_to'    => trim($_GET['date_to']   ?? ''),
    'package_id' => trim($_GET['package_id']?? ''),
    'province'   => trim($_GET['province']  ?? ''),
    'type'       => trim($_GET['type']      ?? ''),   // NEW
];

$transaction_data = fetch_admin_transactions($filters, $current_page, $items_per_page);
$transactions = $transaction_data['transactions'];
$total_items = $transaction_data['total_count'];
$total_pages = $transaction_data['total_pages'];
$current_page = $transaction_data['current_page'];

$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
?>

    <?php include $private_layouts_path . 'admin_header.php'; ?>
    <?php include $private_layouts_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Quản lý Giao dịch</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-invoice-management" class="content-section">
            <h3>Quản lý Giao dịch & Duyệt Thanh Toán</h3>

            <!-- bulk actions -->
            <form id="bulkActionForm" onsubmit="return false;">
                <div class="bulk-actions-bar" style="margin:10px 0; display:flex; gap:10px;">
                    <button type="button" class="btn btn-primary" onclick="PurchaseManagementPageEvents.bulkApproveTransactions()" data-permission="transaction_approve">
                        <i class="fas fa-check-circle"></i> Duyệt đã chọn
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="PurchaseManagementPageEvents.bulkRevertTransactions()" data-permission="transaction_revert">
                        <i class="fas fa-undo-alt"></i> Hủy duyệt đã chọn
                    </button>
                    <button type="button" class="btn btn-danger" onclick="PurchaseManagementPageEvents.bulkRejectTransactions()" data-permission="transaction_reject">
                        <i class="fas fa-times-circle"></i> Từ chối đã chọn
                    </button>
                </div>
            </form>

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
                    <!-- 5. Type filter -->
                    <select id="typeFilter" name="type">
                        <option value=""    <?= $filters['type']===''       ? 'selected':'' ?>>Tất cả loại</option>
                        <option value="purchase" <?= $filters['type']==='purchase' ? 'selected':'' ?>>Đăng ký mới</option>
                        <option value="renewal"  <?= $filters['type']==='renewal'  ? 'selected':'' ?>>Gia hạn</option>
                    </select>
                    <!-- 6. Search input last -->
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
                            <th><input type="checkbox" id="selectAllTx"></th>
                            <th>Mã GD</th>
                            <th>Loại</th>
                            <th>Tỉnh/Thành phố</th>
                            <th>Email</th>
                            <th>Gói</th>
                            <th>Voucher</th>
                            <th>Số tiền</th>
                            <th>Ngày YC</th>
                            <th class="text-center">Xem MC</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="actions text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr id="no-results-row">
                                <td colspan="12">Không tìm thấy giao dịch phù hợp.</td>
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

                                    $typeText = $transaction['transaction_type']==='renewal' ? 'Gia hạn' : 'Đăng ký mới';

                                    $tx_details_for_modal = [
                                        'id' => $transaction_id,
                                        'email' => $transaction['user_email'],
                                        'package_name' => $transaction['package_name'],
                                        'amount' => format_currency($transaction['amount']),
                                        'request_date' => format_datetime($transaction['request_date']),
                                        'status_text' => $status_info['text'],
                                        'status_class' => $status_info['class'],
                                        'proof_image' => $proof_image_url,
                                        'rejection_reason' => $transaction['rejection_reason'] ?? null,
                                        'voucher_code' => $transaction['voucher_code'] ?? null,
                                        'discount_value' => $transaction['discount_value'] ?? null,
                                        'voucher_type' => $transaction['voucher_type'] ?? null,
                                        'voucher_description' => $transaction['voucher_description'] ?? null,
                                        'voucher_max_discount' => $transaction['voucher_max_discount'] ?? null,
                                        'voucher_min_order_value' => $transaction['voucher_min_order_value'] ?? null,
                                        'voucher_start_date' => $transaction['voucher_start_date'] ?? null,
                                        'voucher_end_date' => $transaction['voucher_end_date'] ?? null
                                    ];
                                    $tx_details_json = htmlspecialchars(json_encode($tx_details_for_modal), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr data-transaction-id="<?php echo $transaction_id; ?>" data-status="<?php echo htmlspecialchars($transaction['registration_status']); ?>" data-type="<?= $transaction['transaction_type'] ?>">
                                    <td><input type="checkbox" class="tx-checkbox" value="<?php echo $transaction_id; ?>"></td>
                                    <td>
                                        <a href="#" class="clickable-id" title="Xem chi tiết" onclick='PurchaseManagementPageEvents.showTransactionDetails(<?php echo $tx_details_json; ?>); return false;'>
                                            <?php echo $transaction_id; ?>
                                        </a>
                                    </td>
                                    <td><?= $typeText ?></td>
                                    <td><?php echo htmlspecialchars($transaction['province'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['user_email']?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['package_name']); ?></td>
                                    <td>
                                        <?php if (!empty($transaction['voucher_code'])): ?>
                                            <a href="#" onclick='PurchaseManagementPageEvents.showTransactionDetails(<?php echo $tx_details_json; ?>); return false;'><?php echo htmlspecialchars($transaction['voucher_code']); ?></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount"><?php echo format_currency($transaction['amount']); ?></td>
                                    <td><?php echo format_datetime($transaction['request_date']); ?></td>
                                    <td class="text-center">
                                        <?php if ($proof_image_url): ?>
                                            <button class="btn-icon btn-view-proof" title="Xem Minh Chứng" onclick="PurchaseManagementPageEvents.viewProofModal('<?php echo $transaction_id; ?>', '<?php echo $proof_image_url; ?>')">
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
                                                <button class="btn-icon btn-approve" title="Duyệt" onclick="PurchaseManagementPageEvents.approveTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-reject" title="Từ chối" onclick="PurchaseManagementPageEvents.openRejectTransactionModal('<?php echo $transaction_id; ?>')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-disabled" title="Chờ duyệt" disabled><i class="fas fa-undo-alt"></i></button>
                                            <?php elseif ($is_approved): ?>
                                                <button class="btn-icon btn-disabled" title="Đã duyệt" disabled><i class="fas fa-check-circle"></i></button>
                                                <button class="btn-icon btn-reject" title="Từ chối" onclick="PurchaseManagementPageEvents.openRejectTransactionModal('<?php echo $transaction_id; ?>')" data-permission="transaction_reject"><i class="fas fa-times-circle"></i></button>
                                                <button class="btn-icon btn-revert" title="Hủy duyệt (Về chờ duyệt)" onclick="PurchaseManagementPageEvents.revertTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_revert"><i class="fas fa-undo-alt"></i></button>
                                            <?php elseif ($is_rejected): ?>
                                                <button class="btn-icon btn-approve" title="Duyệt lại" onclick="PurchaseManagementPageEvents.approveTransaction('<?php echo $transaction_id; ?>', this)" data-permission="transaction_approve"><i class="fas fa-check-circle"></i></button>
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

            <?php include $private_layouts_path . 'pagination.php'; ?>
        </form> <!-- This form closes before the pagination was originally -->
    </div> <!-- End #admin-invoice-management -->
</main>

<div id="proofModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="proofModalTitle">Minh chứng Giao dịch</h4>
            <button class="modal-close" onclick="PurchaseManagementPageEvents.closeProofModal()">&times;</button>
        </div>
        <div class="modal-body text-center">
             <img id="proofModalImage" src="" alt="Minh chứng thanh toán">
        </div>
    </div>
</div>

<div id="transaction-details-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modal-title">Chi Tiết Giao Dịch</h4>
            <button class="modal-close" onclick="PurchaseManagementPageEvents.closeDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- NOTE: Ensure your showTransactionDetails JS function populates these new spans -->
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
             <button class="btn btn-secondary" onclick="PurchaseManagementPageEvents.closeDetailsModal()">Đóng</button>
        </div>
    </div>
</div>

<!-- cung cấp basePath cho JS -->
<script>
    window.appConfig = { basePath: '<?php echo $base_url; ?>' };
</script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/purchase/invoice_management.js"></script>
</body>
</html>
<?php include $private_layouts_path . 'admin_footer.php'; ?>