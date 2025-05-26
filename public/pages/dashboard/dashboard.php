<?php
$GLOBALS['required_permission'] = 'dashboard'; // Added permission requirement
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                    = $bootstrap_data['db'];
$base_path             = $bootstrap_data['base_path'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$user_display_name     = $bootstrap_data['user_display_name'];

require_once __DIR__ . '/../../../private/actions/dashboard/fetch_dashboard_data.php';
require_once __DIR__ . '/../../../private/utils/dashboard_helpers.php';

$data = fetch_dashboard_data();

$total_web_users = $data['total_web_users'] ?? 0;
$users_with_package = $data['users_with_package'] ?? 0;
$monthly_sales = $data['monthly_sales'] ?? 0;
$referred_registrations = $data['referred_registrations'] ?? 0;
$total_commission_paid = $data['total_commission_paid'] ?? 0;
$recent_activities = $data['recent_activities'] ?? [];
$new_registrations_chart_data = $data['new_registrations_chart_data'] ?? ['labels' => [], 'data' => []];
$referral_chart_data = $data['referral_chart_data'] ?? ['labels' => [], 'data' => []];
$voucher_details_map = $data['voucher_details_map'] ?? []; // Added this line
$total_vouchers = $data['total_vouchers'] ?? 0; // Added for voucher stats
$used_vouchers = $data['used_vouchers'] ?? 0; // Added for voucher stats
// Pending support requests and inactive stations stats
$pending_support_requests = $data['pending_support_requests'] ?? 0;
$inactive_stations = $data['inactive_stations'] ?? 0;
$top_users = $data['top_users'] ?? []; // Added for top users ranking
$user_package_distribution = $data['user_package_distribution'] ?? ['labels'=>[], 'data'=>[]]; // Added for user distribution
$user_package_ratio = $data['user_package_ratio'] ?? ['with_package'=>0, 'without_package'=>0]; // Added for user ratio

$page_title = 'Admin Dashboard';
?>
<head>
    <meta name="description" content="Bảng điều khiển quản trị để quản lý người dùng, hóa đơn, báo cáo và các cài đặt hệ thống khác.">
</head>
<?php
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

// Include dashboard-specific CSS
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/pages/dashboard.css">

<!-- Main Content Area -->
<main class="content-wrapper">
    <!-- Header Section within Content -->
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <ul class="custom-tabs-nav nav nav-pills flex-wrap">
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link active" data-tab="overview">Tổng quan</a></li>
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="analysis_ranking">Phân tích & Xếp hạng</a></li>
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="activity">Hoạt động</a></li>
    </ul>

    <div class="tab-content" id="overview">
        <?php include __DIR__ . '/tabs/stat_cards.php'; ?>
    </div>

    <div class="tab-content" id="analysis_ranking" style="display:none;">
        <div class="charts-section dashboard-charts-row">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Tổng quan Đăng ký & Giới thiệu (7 ngày)</h3></div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="overviewChart"></canvas></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Phân bổ Người dùng theo Gói</h3></div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="userPackageDistributionChart"></canvas></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Tỷ lệ Người dùng có Gói</h3></div>
                <div class="card-body">
                    <div class="chart-container" style="margin: auto;"><canvas id="userPackageRatioChart"></canvas></div>
                </div>
            </div>
        </div>
        <?php include __DIR__ . '/tabs/top_users.php'; ?>
    </div>

    <div class="tab-content" id="activity" style="display:none;">
        <?php include __DIR__ . '/tabs/activity.php'; ?>
    </div>

</main>

<script>
// Tab switching logic
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.custom-tabs-nav .nav-link');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        contents.forEach(c => c.style.display = (c.id === btn.dataset.tab ? '' : 'none'));
    }));
});
</script>

<script>
  window.dashboardData = {
    newRegistrations: <?php echo json_encode($new_registrations_chart_data); ?>,
    referral:         <?php echo json_encode($referral_chart_data); ?>,
    userDistribution: <?php echo json_encode($user_package_distribution); ?>,
    userRatio:        <?php echo json_encode($user_package_ratio); ?>
  };
</script>
<script src="<?php echo $base_path; ?>public/assets/js/pages/dashboard/dashboard.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
<style>
@media (max-width: 600px) {
    .custom-tabs-nav {
        flex-direction: column;
        gap: 0.5rem;
    }
    .custom-tabs-nav .nav-link {
        width: 100%;
        text-align: left;
        border-radius: 0.25rem;
    }
}
.charts-section .card { /* Existing style for cards */
    margin-bottom: 1rem;
    box-shadow: none;
    border-radius: 0.5rem;
}
.charts-section .card-header { /* Existing style for card headers */
    padding: 0.5rem 1rem;
    font-size: 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e5e5;
}
.charts-section .card-body { /* Existing style for card bodies */
    padding: 1rem;
    background: #f8f9fa;
}
@media (max-width: 600px) {
    .charts-section .card-body {
        padding: 0.5rem;
    }
}

/* New styles for the 3-column chart row */
.dashboard-charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr; /* Three columns for desktop */
    gap: 1rem;
    margin-bottom: 1.5rem; /* Space before the top_users table */
}
@media (max-width: 992px) { /* Tablets: 2 columns */
    .dashboard-charts-row {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 767px) { /* Mobile: 1 column */
    .dashboard-charts-row {
        grid-template-columns: 1fr;
    }
}
.dashboard-charts-row .card .card-title { /* Ensure card titles in charts are consistent */
    font-size: 0.9rem; /* Adjust as needed */
    margin-bottom: 0;
}
.dashboard-charts-row .chart-container {
    position: relative; /* Needed for chart.js responsiveness */
    height: 240px; /* Uniform height for charts in this row */
}
</style>