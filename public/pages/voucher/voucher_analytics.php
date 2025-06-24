<?php
// filepath: public/pages/voucher/voucher_analytics.php

// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                = $bootstrap_data['db'];
$base_path         = $bootstrap_data['base_path'];
$base_url          = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$admin_role        = $bootstrap_data['admin_role'];

// authorization check
require_once BASE_PATH . '/core/auth_check.php';

// --- Includes and Setup ---
require_once BASE_PATH . '/actions/voucher/get_voucher_analytics.php';

// Get analytics data
$voucherModel = new VoucherModel();

// Get date range from filters (empty means no filter)
$start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS);
$end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);

// Fetch analytics data based on filters (supports single or both dates)
$analytics = get_voucher_analytics($start_date, $end_date);

// --- Page Setup for Header/Sidebar ---
$page_title = 'Phân tích Voucher';

// CSS files for this page
$additional_css = [
    'components/stat-card.css',
    'components/analytics-card.css', 
    'components/progress-bar.css',
    'pages/voucher/voucher_analytics.css'
];

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <!-- Content Header -->
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="voucher-analytics" class="content-section">
        <div class="header-actions">
            <h3>Phân tích Voucher</h3>
            <a href="<?php echo $base_path; ?>public/pages/voucher/voucher_management.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại quản lý voucher
            </a>
        </div>

        <!-- Date Range Filter -->
        <form method="GET" action="" class="filter-bar">
            <div class="filter-group">
                <label for="start_date">Từ ngày:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars(
                    $start_date ?: ''
                ); ?>">
            </div>
            <div class="filter-group">
                <label for="end_date">Đến ngày:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars(
                    $end_date ?: ''
                ); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href=window.location.pathname">
                    <i class="fas fa-times"></i> Xóa lọc
                </button>
            </div>
        </form>

        <!-- Overview Cards -->
        <div class="stats-grid">
            <div class="stat-card analytics-style">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo number_format($analytics['overview']['total_vouchers']); ?></h4>
                    <p>Tổng voucher</p>
                </div>
            </div>
            <div class="stat-card analytics-style">
                <div class="stat-icon bg-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo number_format($analytics['overview']['active_vouchers']); ?></h4>
                    <p>Voucher hoạt động</p>
                </div>
            </div>
            <div class="stat-card analytics-style">
                <div class="stat-icon bg-orange">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h4><?php echo number_format($analytics['overview']['total_usage']); ?></h4>
                    <p>Lượt sử dụng</p>
                </div>
            </div>
            <div class="stat-card analytics-style">
                <div class="stat-icon bg-purple">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h4><?php 
                        $usage_rate = 0;
                        if ($analytics['overview']['total_available'] > 0) {
                            $usage_rate = round(($analytics['overview']['total_usage'] * 100) / $analytics['overview']['total_available'], 1);
                        }
                        echo $usage_rate . '%';
                    ?></h4>
                    <p>Tỷ lệ sử dụng</p>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="analytics-grid">
            <!-- Top Vouchers -->
            <div class="analytics-card">
                <h4><i class="fas fa-trophy"></i> Top Voucher được sử dụng nhiều nhất</h4>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã voucher</th>
                                <th>Mô tả</th>
                                <th>Loại</th>
                                <th>Đã dùng</th>
                                <th>Tổng số</th>
                                <th>Tỷ lệ sử dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($analytics['top_vouchers'])): ?>
                                <?php foreach ($analytics['top_vouchers'] as $voucher): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($voucher['code']); ?></td>
                                    <td><?php echo htmlspecialchars($voucher['description']); ?></td>
                                    <td><?php echo htmlspecialchars(get_voucher_type_display($voucher['voucher_type'])); ?></td>
                                    <td><?php echo number_format($voucher['used_quantity']); ?></td>
                                    <td><?php echo $voucher['quantity'] ? number_format($voucher['quantity']) : '<span class="cell-text-special">∞</span>'; ?></td>
                                    <td>
                                        <?php if ($voucher['usage_rate']): ?>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo min(100, $voucher['usage_rate']); ?>%"></div>
                                                <span><?php echo $voucher['usage_rate']; ?>%</span>
                                            </div>
                                        <?php else: ?>
                                            <span class="cell-text-special cell-text-na">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6">Không có dữ liệu</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Voucher by Type -->
            <div class="analytics-card">
                <h4><i class="fas fa-chart-pie"></i> Phân tích theo loại voucher</h4>
                <div class="type-stats">
                    <?php if (!empty($analytics['by_type'])): ?>
                        <?php foreach ($analytics['by_type'] as $type): ?>
                        <div class="type-stat-item">
                            <div class="type-info">
                                <strong><?php echo htmlspecialchars(get_voucher_type_display($type['voucher_type'])); ?></strong>
                                <span><?php echo number_format($type['count']); ?> voucher</span>
                            </div>
                            <div class="type-usage">
                                <span class="usage-total"><?php echo number_format($type['total_used']); ?> lượt sử dụng</span>
                                <span class="usage-avg">Trung bình: <?php echo number_format($type['avg_used'], 1); ?> lượt/voucher</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Không có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Expiring Soon -->
        <?php if (!empty($analytics['expiring_soon'])): ?>
        <div class="analytics-card warning-card">
            <h4><i class="fas fa-exclamation-triangle"></i> Voucher sắp hết hạn (7 ngày tới)</h4>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã voucher</th>
                            <th>Mô tả</th>
                            <th>Ngày hết hạn</th>
                            <th>Đã dùng/Tổng số</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($analytics['expiring_soon'] as $voucher): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($voucher['code']); ?></td>
                            <td><?php echo htmlspecialchars($voucher['description']); ?></td>
                            <td>
                                <span class="expires-soon"><?php echo format_date($voucher['end_date']); ?></span>
                            </td>
                            <td>
                                <?php echo number_format($voucher['used_quantity']); ?>/<?php echo $voucher['quantity'] ? number_format($voucher['quantity']) : '∞'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include $private_layouts_path . 'admin_footer.php'; ?>
