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
require_once __DIR__ . '/../../../private/core/auth_check.php';

// --- Includes and Setup ---
require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/classes/VoucherModel.php';

// Get analytics data
$voucherModel = new VoucherModel();

// Get date range from filters
$start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS) ?: date('Y-m-01'); // First day of current month
$end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS) ?: date('Y-m-d'); // Today

// Fetch analytics data
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

function get_voucher_analytics($start_date, $end_date) {
    global $db;
    
    $analytics = [];
    
    // 1. Tổng quan voucher
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_vouchers,
            SUM(CASE WHEN is_active = 1 AND end_date >= NOW() THEN 1 ELSE 0 END) as active_vouchers,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_vouchers,
            SUM(CASE WHEN end_date < NOW() THEN 1 ELSE 0 END) as expired_vouchers,
            SUM(used_quantity) as total_usage,
            SUM(quantity) as total_available
        FROM voucher 
        WHERE created_at BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
    $analytics['overview'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 2. Top vouchers được sử dụng nhiều nhất
    $stmt = $db->prepare("
        SELECT code, description, voucher_type, used_quantity, quantity,
               ROUND((used_quantity * 100.0 / NULLIF(quantity, 0)), 2) as usage_rate
        FROM voucher 
        WHERE created_at BETWEEN ? AND ?
        ORDER BY used_quantity DESC 
        LIMIT 10
    ");
    $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
    $analytics['top_vouchers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Phân tích theo loại voucher
    $stmt = $db->prepare("
        SELECT 
            voucher_type,
            COUNT(*) as count,
            SUM(used_quantity) as total_used,
            AVG(used_quantity) as avg_used
        FROM voucher 
        WHERE created_at BETWEEN ? AND ?
        GROUP BY voucher_type
    ");
    $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
    $analytics['by_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Voucher sắp hết hạn (trong 7 ngày tới)
    $stmt = $db->prepare("
        SELECT code, description, end_date, used_quantity, quantity
        FROM voucher 
        WHERE end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        AND is_active = 1
        ORDER BY end_date ASC
    ");
    $stmt->execute();
    $analytics['expiring_soon'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Thống kê sử dụng theo tháng (3 tháng gần nhất)
    $stmt = $db->prepare("
        SELECT 
            DATE_FORMAT(uvu.used_at, '%Y-%m') as month,
            COUNT(*) as usage_count
        FROM user_voucher_usage uvu
        JOIN voucher v ON uvu.voucher_id = v.id
        WHERE uvu.used_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
        GROUP BY DATE_FORMAT(uvu.used_at, '%Y-%m')
        ORDER BY month DESC
    ");
    $stmt->execute();
    $analytics['monthly_usage'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $analytics;
}
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
            <div class="form-group">
                <label for="start_date">Từ ngày:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div class="form-group">
                <label for="end_date">Đến ngày:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Lọc
            </button>
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
