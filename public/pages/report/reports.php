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

?>
<?php include $private_layouts_path . 'admin_header.php'; ?>
<?php include $private_layouts_path . 'admin_sidebar.php'; ?>
<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="admin-reports" class="content-section">
        <div class="mb-6 bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-3">Bộ lọc chung</h3>
            <div class="filter-bar flex items-end gap-2">
                <form id="report-filter-form" method="GET" action="" class="flex items-end gap-2">
                    <input type="date" id="report-start-date" name="start_date"
                           value="<?php echo htmlspecialchars($start_date); ?>" placeholder="Từ ngày">
                    <input type="date" id="report-end-date" name="end_date"
                           value="<?php echo htmlspecialchars($end_date); ?>" placeholder="Đến ngày">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Xem báo cáo
                    </button>
                    <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Xóa lọc
                    </a>
                </form>
                <form id="export-report-excel" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php" class="inline-block">
                    <input type="hidden" name="action" value="export_report_excel">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    <button type="submit" class="btn btn-success ml-2">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </button>
                </form>
            </div>
        </div>
        <!-- Reports cards: using stats-grid/stat-card -->
        <div class="stats-grid">
            <!-- Người dùng -->
            <div class="stat-card">
                <div class="icon bg-blue-200 text-blue-600"><i class="fas fa-users"></i></div>
                <div>
                    <h3>Người dùng</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng số đăng ký:</span> <strong><?php echo $total_registrations; ?></strong></div>
                        <div class="flex justify-between"><span>Đăng ký mới (kỳ BC):</span> <strong><?php echo $new_registrations; ?></strong></div>
                        <div class="flex justify-between"><span>Tài khoản hoạt động:</span> <strong><?php echo $active_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>Tài khoản bị khóa:</span> <strong><?php echo $locked_accounts; ?></strong></div>
                    </div>
                </div>
            </div>
            <!-- Tài khoản đo đạc -->
            <div class="stat-card">
                <div class="icon bg-green-200 text-green-600"><i class="fas fa-ruler-combined"></i></div>
                <div>
                    <h3>Tài khoản đo đạc</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng số TK đang HĐ:</span> <strong><?php echo $active_survey_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK kích hoạt mới (kỳ BC):</span> <strong><?php echo $new_active_survey_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK sắp hết hạn (30 ngày):</span> <strong><?php echo $expiring_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK đã hết hạn (kỳ BC):</span> <strong><?php echo $expired_accounts; ?></strong></div>
                    </div>
                </div>
            </div>
            <!-- Giao dịch -->
            <div class="stat-card">
                <div class="icon bg-yellow-200 text-yellow-600"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h3>Giao dịch</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng doanh số (kỳ BC):</span> <strong><?php echo format_currency($total_sales); ?></strong></div>
                        <div class="flex justify-between"><span>Số GD thành công:</span> <strong><?php echo $completed_transactions; ?></strong></div>
                        <div class="flex justify-between"><span>Số GD chờ duyệt:</span> <strong><?php echo $pending_transactions; ?></strong></div>
                        <div class="flex justify-between"><span>Số GD bị từ chối:</span> <strong><?php echo $failed_transactions; ?></strong></div>
                    </div>
                </div>
            </div>
            <!-- Giới thiệu -->
            <div class="stat-card">
                <div class="icon bg-indigo-200 text-indigo-600"><i class="fas fa-network-wired"></i></div>
                <div>
                    <h3>Giới thiệu</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Lượt giới thiệu mới (kỳ BC):</span> <strong><?php echo $new_referrals; ?></strong></div>
                        <div class="flex justify-between"><span>Hoa hồng phát sinh (kỳ BC):</span> <strong><?php echo format_currency($commission_generated); ?></strong></div>
                        <div class="flex justify-between"><span>Hoa hồng đã thanh toán (kỳ BC):</span> <strong><?php echo format_currency($commission_paid); ?></strong></div>
                        <div class="flex justify-between"><span>Tổng HH chờ thanh toán:</span> <strong><?php echo format_currency($commission_pending); ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="<?php echo $base_url; ?>public/assets/js/pages/report/reports.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>