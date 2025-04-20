<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\reports.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// TODO: Fetch actual report data based on filters

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Báo cáo Tổng hợp</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-reports" class="content-section">
            <div class="mb-6 bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3">Bộ lọc chung</h3>
                <form id="report-filter-form">
                    <div class="flex flex-wrap gap-3 sm:gap-4 items-end">
                        <div>
                            <label for="report-start-date" class="block text-xs font-medium text-gray-600 mb-1">Từ ngày</label>
                            <input type="date" id="report-start-date" name="start_date" class="text-sm p-2 min-w-[140px]">
                        </div>
                        <div>
                            <label for="report-end-date" class="block text-xs font-medium text-gray-600 mb-1">Đến ngày</label>
                            <input type="date" id="report-end-date" name="end_date" class="text-sm p-2 min-w-[140px]">
                        </div>
                        <div>
                            <button type="submit" class="btn-primary"><i class="fas fa-filter mr-1"></i> Xem báo cáo</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Report Card: Người dùng -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-users text-blue-600"></i> Người dùng</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng số đăng ký:</span> <strong class="font-medium">150</strong></div>
                        <div class="flex justify-between"><span>Đăng ký mới (kỳ BC):</span> <strong class="font-medium">25</strong></div>
                        <div class="flex justify-between"><span>Tài khoản hoạt động:</span> <strong class="font-medium">130</strong></div>
                        <div class="flex justify-between"><span>Tài khoản bị khóa:</span> <strong class="font-medium">5</strong></div>
                    </div>
                </div>

                <!-- Report Card: Tài khoản đo đạc -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-ruler-combined text-primary-600"></i> Tài khoản đo đạc</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng số TK đang HĐ:</span> <strong class="font-medium">280</strong></div>
                        <div class="flex justify-between"><span>TK kích hoạt mới (kỳ BC):</span> <strong class="font-medium">40</strong></div>
                        <div class="flex justify-between"><span>TK sắp hết hạn (30 ngày):</span> <strong class="font-medium">15</strong></div>
                        <div class="flex justify-between"><span>TK đã hết hạn (kỳ BC):</span> <strong class="font-medium">10</strong></div>
                    </div>
                </div>

                <!-- Report Card: Giao dịch -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-yellow-600"></i> Giao dịch</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng doanh số (kỳ BC):</span> <strong class="font-medium">25,500,000đ</strong></div>
                        <div class="flex justify-between"><span>Số GD thành công:</span> <strong class="font-medium">35</strong></div>
                        <div class="flex justify-between"><span>Số GD chờ duyệt:</span> <strong class="font-medium">5</strong></div>
                        <div class="flex justify-between"><span>Số GD bị từ chối:</span> <strong class="font-medium">2</strong></div>
                    </div>
                </div>

                <!-- Report Card: Giới thiệu -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-network-wired text-indigo-600"></i> Giới thiệu</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Lượt giới thiệu mới (kỳ BC):</span> <strong class="font-medium">18</strong></div>
                        <div class="flex justify-between"><span>Hoa hồng phát sinh (kỳ BC):</span> <strong class="font-medium">1,850,000đ</strong></div>
                        <div class="flex justify-between"><span>Hoa hồng đã thanh toán (kỳ BC):</span> <strong class="font-medium">1,200,000đ</strong></div>
                        <div class="flex justify-between"><span>Tổng HH chờ thanh toán:</span> <strong class="font-medium">2,100,000đ</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('report-filter-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const startDate = document.getElementById('report-start-date').value;
        const endDate = document.getElementById('report-end-date').value;
        console.log('Fetching report data from', startDate, 'to', endDate);
        // Add AJAX logic to fetch and update report data
    });
</script>
