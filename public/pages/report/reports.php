<?php
// filepath: public\pages\reports.php
$page_title             = 'Báo cáo';
$bootstrap_data         = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                      = $bootstrap_data['db'];
$base_url                = $bootstrap_data['base_url'];
$private_layouts_path   = $bootstrap_data['private_layouts_path'];
$user_display_name       = $bootstrap_data['user_display_name'];
$admin_role              = $bootstrap_data['admin_role'];

// Authorization check is now handled by admin_header.php

$pdo = $db;

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

// Include the data processing logic
require_once __DIR__ . '/../../../private/actions/report/process_reports_data.php';

// Define stat card configurations for loop rendering
$statSections = [
    [
        'title' => 'Người dùng',
        'icon_bg' => 'bg-blue-200',
        'icon_text' => 'text-blue-600',
        'icon_class' => 'fas fa-users',
        'items' => [
            ['label' => 'Tổng số đăng ký:', 'value' => $total_registrations],
            ['label' => 'Đăng ký mới (kỳ BC):', 'value' => $new_registrations],
            ['label' => 'Tài khoản hoạt động:', 'value' => $active_accounts],
            ['label' => 'Tài khoản bị khóa:', 'value' => $locked_accounts],
        ],
    ],
    [
        'title' => 'Tài khoản đo đạc',
        'icon_bg' => 'bg-green-200',
        'icon_text' => 'text-green-600',
        'icon_class' => 'fas fa-ruler-combined',
        'items' => [
            ['label' => 'Tổng số TK đang HĐ:', 'value' => $active_survey_accounts],
            ['label' => 'TK kích hoạt mới (kỳ BC):', 'value' => $new_active_survey_accounts],
            ['label' => 'TK sắp hết hạn (30 ngày):', 'value' => $expiring_accounts],
            ['label' => 'TK đã hết hạn (kỳ BC):', 'value' => $expired_accounts],
        ],
    ],
    [
        'title' => 'Giao dịch',
        'icon_bg' => 'bg-yellow-200',
        'icon_text' => 'text-yellow-600',
        'icon_class' => 'fas fa-file-invoice-dollar',
        'items' => [
            ['label' => 'Tổng doanh số (kỳ BC):', 'value' => format_currency($total_sales)],
            ['label' => 'Số GD thành công:', 'value' => $completed_transactions],
            ['label' => 'Số GD chờ duyệt:', 'value' => $pending_transactions],
            ['label' => 'Số GD bị từ chối:', 'value' => $failed_transactions],
        ],
    ],
    [
        'title' => 'Giới thiệu',
        'icon_bg' => 'bg-indigo-200',
        'icon_text' => 'text-indigo-600',
        'icon_class' => 'fas fa-network-wired',
        'items' => [
            ['label' => 'Lượt giới thiệu mới (kỳ BC):', 'value' => $new_referrals],
            ['label' => 'Hoa hồng phát sinh (kỳ BC):', 'value' => format_currency($commission_generated)],
            ['label' => 'Hoa hồng đã thanh toán (kỳ BC):', 'value' => format_currency($commission_paid)],
            ['label' => 'Tổng HH chờ thanh toán:', 'value' => format_currency($commission_pending)],
        ],
    ],
];

// Define chart configurations for loop rendering
$chartSections = [
    ['id' => 'revenueTrendChart', 'title' => 'Xu hướng Doanh thu'],
    ['id' => 'transactionStatusChart', 'title' => 'Phân tích Trạng thái Giao dịch'],
    ['id' => 'commissionAnalyticsChart', 'title' => 'Phân tích Hoa hồng'],
    ['id' => 'overviewChart', 'title' => 'Tổng quan Đăng ký & Giới thiệu'],
    ['id' => 'userPackageDistributionChart', 'title' => 'Phân bổ Người dùng theo Gói'],
    ['id' => 'userPackageRatioChart', 'title' => 'Tỷ lệ Người dùng có Gói'],
];
?>
<?php include $private_layouts_path . 'admin_header.php'; ?>
<?php include $private_layouts_path . 'admin_sidebar.php'; ?>

<!-- Include dashboard-specific CSS for charts -->
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/pages/dashboard.css">
<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="admin-reports" class="content-section">
        <?php include __DIR__ . '/partials/filter_section.php'; ?>

        <!-- Tab Navigation -->
        <ul class="custom-tabs-nav nav nav-pills flex-wrap">
            <li class="nav-item"><a href="javascript:void(0)" class="nav-link active" data-tab="overview">Tổng quan</a></li>
            <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="charts">Biểu đồ & Phân tích</a></li>
        </ul>

        <!-- Tab Content: Overview -->
        <div class="tab-content" id="overview">
            <?php include __DIR__ . '/partials/overview_tab_content.php'; ?>
        </div>

        <!-- Tab Content: Charts & Analysis -->
        <div class="tab-content" id="charts" style="display:none;">
            <?php include __DIR__ . '/partials/charts_tab_content.php'; ?>
        </div>
    </div>
</main>
<script>
  window.reportChartData = {
    newRegistrations: <?php echo json_encode($new_registrations_chart_data); ?>,
    referral:          <?php echo json_encode($referral_chart_data); ?>,
    userDistribution:  <?php echo json_encode($user_package_distribution); ?>,
    userRatio:         <?php echo json_encode($user_package_ratio); ?>,
    revenueTrend:      <?php echo json_encode($revenue_trend_chart_data); ?>,
    transactionStatus: <?php echo json_encode($transaction_status_chart_data); ?>,
    commissionAnalytics: <?php echo json_encode($commission_analytics_chart_data); ?>
  };
</script>
<script type="module" src="<?php echo $base_url; ?>public/assets/js/pages/report/reports.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>