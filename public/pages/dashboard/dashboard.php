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
$voucher_details_map = $data['voucher_details_map'] ?? []; // Added this line
$total_vouchers = $data['total_vouchers'] ?? 0; // Added for voucher stats
$used_vouchers = $data['used_vouchers'] ?? 0; // Added for voucher stats
// Pending support requests and inactive stations stats
$pending_support_requests = $data['pending_support_requests'] ?? 0;
$inactive_stations = $data['inactive_stations'] ?? 0;
$top_users = $data['top_users'] ?? []; // Added for top users ranking

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
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="analysis_ranking">Xếp hạng</a></li>
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="activity">Hoạt động</a></li>
    </ul>

    <div class="tab-content" id="overview">
        <?php include __DIR__ . '/tabs/stat_cards.php'; ?>
    </div>

    <div class="tab-content" id="analysis_ranking" style="display:none;">
        <?php include __DIR__ . '/tabs/top_users.php'; ?>
    </div>

    <div class="tab-content" id="activity" style="display:none;">
        <?php include __DIR__ . '/tabs/activity.php'; ?>
    </div>

</main>


<script src="<?php echo $base_path; ?>public/assets/js/pages/dashboard/dashboard.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>