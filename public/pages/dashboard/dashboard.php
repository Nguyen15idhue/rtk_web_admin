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
$active_survey_accounts = $data['active_survey_accounts'] ?? 0;
$monthly_sales = $data['monthly_sales'] ?? 0;
$total_referrers = $data['total_referrers'] ?? 0;
$referred_registrations = $data['referred_registrations'] ?? 0;
$total_commission_paid = $data['total_commission_paid'] ?? 0;
$recent_activities = $data['recent_activities'] ?? [];
$new_registrations_chart_data = $data['new_registrations_chart_data'] ?? ['labels' => [], 'data' => []];
$referral_chart_data = $data['referral_chart_data'] ?? ['labels' => [], 'data' => []];

$page_title = 'Admin Dashboard';

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';

// Include dashboard-specific CSS
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/pages/dashboard.css">

<!-- Main Content Area -->
<main class="content-wrapper">
    <!-- Header Section within Content -->
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <ul class="custom-tabs-nav">
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link active" data-tab="overview">Tổng quan</a></li>
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="charts">Biểu đồ</a></li>
        <li class="nav-item"><a href="javascript:void(0)" class="nav-link" data-tab="activity">Hoạt động</a></li>
    </ul>

    <div class="tab-content" id="overview">
        <?php include __DIR__ . '/tabs/stat_cards.php'; ?>
    </div>
    <div class="tab-content" id="charts" style="display:none;">
        <?php include __DIR__ . '/tabs/charts.php'; ?>
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
    referral:         <?php echo json_encode($referral_chart_data); ?>
  };
</script>
<script src="<?php echo $base_path; ?>public/assets/js/pages/dashboard/dashboard.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>