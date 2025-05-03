<?php
$bootstrap_data = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$db                    = $bootstrap_data['db'];
$base_path             = $bootstrap_data['base_path'];
$private_includes_path = $bootstrap_data['private_includes_path'];
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

include $private_includes_path . 'admin_header.php';
include $private_includes_path . 'admin_sidebar.php';
?>

<!-- Main Content Area -->
<main class="content-wrapper">
    <!-- Header Section within Content -->
    <div class="content-header">
        <h2>Admin Dashboard</h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <!-- Link to Profile Settings (Adjust path if needed) -->
            <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <!-- Logout Link (Adjust path if needed) -->
            <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php" class="text-gray-600">Đăng xuất</a>
        </div>
    </div>

    <!-- Stats Grid - Adapted for Admin Data -->
    <div class="stats-grid">
        <!-- Card: Người dùng Web -->
        <div class="stat-card">
            <i class="icon fas fa-users" style="color: #3b82f6; background-color: #dbeafe;"></i>
            <div>
                <h3>Người dùng Web</h3>
                <p class="value"><?php echo number_format($total_web_users); ?></p>
            </div>
        </div>
        <!-- Card: TK đã mua gói -->
        <div class="stat-card">
            <i class="icon fas fa-user-check" style="color: #10b981; background-color: #d1fae5;"></i>
            <div>
                <h3>TK đã mua gói</h3>
                <p class="value"><?php echo number_format($users_with_package); ?></p>
            </div>
        </div>
        <!-- Card: TK đo đạc HĐ -->
        <div class="stat-card">
        <i class="icon fas fa-ruler-combined" style="color: #6366f1; background-color: #e0e7ff;"></i>
            <div>
                <h3>TK đo đạc HĐ</h3>
                <p class="value"><?php echo number_format($active_survey_accounts); ?></p>
            </div>
        </div>
        <!-- Card: Doanh số (Tháng) -->
        <div class="stat-card">
        <i class="icon fas fa-dollar-sign" style="color: #f59e0b; background-color: #fef3c7;"></i>
            <div>
                <h3>Doanh số (Tháng)</h3>
                <p class="value"><?php echo format_number_short($monthly_sales); ?></p>
            </div>
        </div>
        <!-- Card: Người giới thiệu -->
        <div class="stat-card">
        <i class="icon fas fa-user-group" style="color: #4f46e5; background-color: #e0e7ff;"></i>
            <div>
                <h3>Người giới thiệu</h3>
                <p class="value"><?php echo number_format($total_referrers); ?></p>
            </div>
        </div>
        <!-- Card: ĐK từ GT -->
        <div class="stat-card">
        <i class="icon fas fa-user-tag" style="color: #a855f7; background-color: #f3e8ff;"></i>
            <div>
                <h3>ĐK từ GT</h3>
                <p class="value"><?php echo number_format($referred_registrations); ?></p>
            </div>
        </div>
        <!-- Card: Tổng HH đã trả -->
        <div class="stat-card">
        <i class="icon fas fa-coins" style="color: #ec4899; background-color: #fce7f3;"></i>
            <div>
                <h3>Tổng HH đã trả</h3>
                <p class="value"><?php echo format_number_short($total_commission_paid); ?></p>
            </div>
        </div>
    </div> <!-- End Stats Grid -->

    <!-- Charts Section -->
    <div class="charts-grid">
        <section class="content-section">
            <h3>Đăng ký mới (7 ngày)</h3>
            <div class="chart-container">
                <canvas id="newRegistrationsChart"></canvas>
            </div>
        </section>
        <section class="content-section">
            <h3>Giới thiệu HĐ (7 ngày)</h3>
            <div class="chart-container">
                <canvas id="referralChart"></canvas>
            </div>
        </section>
    </div><!-- End Charts Section -->

    <!-- Recent Activity Section -->
    <section class="recent-activity content-section">
        <h3>Hoạt động hệ thống gần đây</h3>
        <div class="activity-list" id="activity-list">
            <?php if (empty($recent_activities)): ?>
                <p class="text-gray-500 italic">Không có hoạt động nào gần đây.</p>
            <?php else: ?>
                <?php foreach ($recent_activities as $log): ?>
                    <?php $activity = format_activity_log($log); ?>
                    <div class="activity-item" style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">
                        <i class="<?php echo $activity['icon']; ?>" style="margin-top: 0.125rem; flex-shrink: 0; width: 1.5rem; text-align: center;"></i>
                        <div style="flex-grow: 1;">
                            <p style="margin: 0; line-height: 1.4; font-size: 0.9rem;"><?php echo $activity['message']; ?></p>
                            <small style="color: #6b7280; font-size: 0.8em;"><?php echo $activity['time']; ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
                <style>
                    .activity-item:last-child {
                        border-bottom: none;
                        margin-bottom: 0;
                    }
                </style>
            <?php endif; ?>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  window.dashboardData = {
    newRegistrations: <?php echo json_encode($new_registrations_chart_data); ?>,
    referral:         <?php echo json_encode($referral_chart_data); ?>
  };
</script>
<script src="<?php echo $base_path; ?>public/assets/js/pages/dashboard/dashboard.js"></script>
<?php include $private_includes_path . 'admin_footer.php'; ?>