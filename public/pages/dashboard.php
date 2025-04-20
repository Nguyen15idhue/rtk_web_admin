<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\dashboard.php

// Include necessary files
require_once __DIR__ . '/../../private/actions/dashboard/fetch_dashboard_data.php';
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php'; // Include the helpers
$private_includes_path = __DIR__ . '/../../private/includes/';
// Fetch dashboard data (Admin data)
$data = fetch_dashboard_data();

// Assign data to variables for use in the HTML template
$total_web_users = $data['total_web_users'];
$users_with_package = $data['users_with_package'];
$active_survey_accounts = $data['active_survey_accounts'];
$monthly_sales = $data['monthly_sales'];
$total_referrers = $data['total_referrers'];
$referred_registrations = $data['referred_registrations'];
$total_commission_paid = $data['total_commission_paid'];
$recent_activities = $data['recent_activities'];

// Get display name (assuming admin might have a session name)
$user_display_name = $_SESSION['admin_username'] ?? 'Admin'; // Adjust session variable if needed

// Base path for links (adjust if necessary)
$base_path = '/';

?>

<!-- Wrap sidebar and content in the dashboard-wrapper -->
<div class="dashboard-wrapper">
    <!-- Include Sidebar -->
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <!-- Apply structure from the snippet, using Admin data -->
    <main class="content-wrapper">
        <!-- Header Section within Content -->
        <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="text-2xl font-semibold">Admin Dashboard</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight" style="color: var(--primary-600); font-weight: 600;"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <!-- Link to Profile Settings (Adjust path if needed) -->
                <a href="<?php echo $base_path; ?>public/pages/profile.php" style="margin-left: 15px; color: var(--primary-600); text-decoration: none;">Hồ sơ</a>
                <!-- Logout Link (Adjust path if needed) -->
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php" style="margin-left: 15px; color: var(--gray-600); text-decoration: none;">Đăng xuất</a>
            </div>
        </div>

        <!-- Stats Grid - Adapted for Admin Data -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <!-- Card: Người dùng Web -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
                <i class="icon fas fa-users" style="font-size: 1.5rem; color: #3b82f6; background-color: #dbeafe; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">Người dùng Web</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo number_format($total_web_users); ?></p>
                </div>
            </div>
            <!-- Card: TK đã mua gói -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
                <i class="icon fas fa-user-check" style="font-size: 1.5rem; color: #10b981; background-color: #d1fae5; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">TK đã mua gói</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo number_format($users_with_package); ?></p>
                </div>
            </div>
            <!-- Card: TK đo đạc HĐ -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
            <i class="icon fas fa-ruler-combined" style="font-size: 1.5rem; color: #6366f1; background-color: #e0e7ff; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">TK đo đạc HĐ</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo number_format($active_survey_accounts); ?></p>
                </div>
            </div>
            <!-- Card: Doanh số (Tháng) -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
            <i class="icon fas fa-dollar-sign" style="font-size: 1.5rem; color: #f59e0b; background-color: #fef3c7; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">Doanh số (Tháng)</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo format_number_short($monthly_sales); ?></p>
                </div>
            </div>
            <!-- Card: Người giới thiệu -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
            <i class="icon fas fa-users-rays" style="font-size: 1.5rem; color: #4f46e5; background-color: #e0e7ff; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">Người giới thiệu</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo number_format($total_referrers); ?></p>
                </div>
            </div>
            <!-- Card: ĐK từ GT -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
            <i class="icon fas fa-user-tag" style="font-size: 1.5rem; color: #a855f7; background-color: #f3e8ff; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">ĐK từ GT</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo number_format($referred_registrations); ?></p>
                </div>
            </div>
            <!-- Card: Tổng HH đã trả -->
            <div class="stat-card" style="background-color: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.75rem;">
            <i class="icon fas fa-coins" style="font-size: 1.5rem; color: #ec4899; background-color: #fce7f3; padding: 0.75rem; border-radius: 50%;"></i>
                <div>
                    <h3 style="font-size: 0.8rem; color: #4b5563; margin-bottom: 0.25rem;">Tổng HH đã trả</h3>
                    <p class="value" style="font-size: 1.25rem; font-weight: bold; color: #111827;"><?php echo format_number_short($total_commission_paid); ?></p>
                </div>
            </div>
        </div> <!-- End Stats Grid -->

        <!-- Charts Section - Placeholder -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200" style="background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4" style="font-size: 1.1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Đăng ký mới (7 ngày)</h3>
                <div class="chart-container" style="min-height: 200px; display: flex; align-items: center; justify-content: center; color: #6b7280;"> Biểu đồ đăng ký (Cần JS) </div>
            </div>
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200" style="background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4" style="font-size: 1.1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Giới thiệu HĐ (7 ngày)</h3>
                <div class="chart-container" style="min-height: 200px; display: flex; align-items: center; justify-content: center; color: #6b7280;"> Biểu đồ giới thiệu (Cần JS) </div>
            </div>
        </div><!-- End Charts Section -->

        <!-- Recent Activity Section -->
        <section class="recent-activity" style="background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Hoạt động hệ thống gần đây</h3>
            <div class="activity-list" id="activity-list" style="max-height: 300px; overflow-y: auto; padding-right: 0.5rem;">
                <?php if (empty($recent_activities)): ?>
                    <p class="text-gray-500 italic" style="color: #6b7280; font-style: italic;">Không có hoạt động nào gần đây.</p>
                <?php else: ?>
                    <?php foreach ($recent_activities as $log): ?>
                        <?php $activity = format_activity_log($log); // Use existing helper ?>
                        <div class="activity-item" style="display: flex; align-items: flex-start; padding-bottom: 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <i class="<?php echo $activity['icon']; ?>" style="margin-top: 0.125rem; margin-right: 0.75rem; width: 1rem; text-align: center; flex-shrink: 0; color: #6b7280;"></i>
                            <div style="flex-grow: 1;">
                                <p style="font-size: 0.9rem; color: #374151; margin-bottom: 0.1rem;"><?php echo $activity['message']; ?></p>
                                <small style="font-size: 0.75rem; color: #6b7280;"><?php echo $activity['time']; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Add border to last item if needed -->
                    <style> .activity-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; } </style>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div> <!-- End dashboard-wrapper -->

<!-- Note: Sidebar and Footer includes should be handled by the main layout file -->
<!-- Removed JS from here, it should be in a global JS file or footer include -->