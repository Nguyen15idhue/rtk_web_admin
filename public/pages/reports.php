<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\reports.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}
$bootstrap_data         = require_once __DIR__ . '/../../private/includes/page_bootstrap.php';
$db                      = $bootstrap_data['db'];
$base_url                = $bootstrap_data['base_url'];
$private_includes_path   = $bootstrap_data['private_includes_path'];
$user_display_name       = $bootstrap_data['user_display_name'];

$pdo = $db;

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

// Total registrations
$stmt = $pdo->query("SELECT COUNT(id) as count FROM registration WHERE deleted_at IS NULL");
$total_registrations = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// New registrations in period
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM registration WHERE deleted_at IS NULL AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$new_registrations = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Active accounts
$stmt = $pdo->query("SELECT COUNT(id) as count FROM registration WHERE status = 'active' AND deleted_at IS NULL");
$active_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Locked accounts (non-active)
$stmt = $pdo->query("SELECT COUNT(id) as count FROM registration WHERE status != 'active' AND deleted_at IS NULL");
$locked_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

// Active survey accounts
$stmt = $pdo->query("SELECT COUNT(sa.id) as count FROM survey_account sa JOIN registration r ON sa.registration_id = r.id WHERE sa.enabled = 1 AND sa.deleted_at IS NULL AND r.deleted_at IS NULL");
$active_survey_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// New active survey accounts in period
$stmt = $pdo->prepare("SELECT COUNT(sa.id) as count FROM survey_account sa JOIN registration r ON sa.registration_id = r.id WHERE sa.enabled = 1 AND r.created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$new_active_survey_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Accounts expiring in 30 days
$stmt = $pdo->query("SELECT COUNT(id) as count FROM registration WHERE end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)");
$expiring_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Accounts expired in period
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM registration WHERE end_time BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$expired_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

// Transactions
// Total sales in period
$stmt = $pdo->prepare("SELECT SUM(r.total_price) as total FROM registration r LEFT JOIN payment p ON r.id = p.registration_id WHERE r.deleted_at IS NULL AND r.created_at BETWEEN :start AND :end AND (r.status = 'active' OR p.confirmed = 1)");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$total_sales = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
// Completed transactions
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'completed' AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$completed_transactions = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Pending transactions
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'pending' AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$pending_transactions = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Failed transactions
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM transaction_history WHERE status = 'failed' AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$failed_transactions = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

// Referrals
// New referrals in period
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM registration WHERE collaborator_id IS NOT NULL AND deleted_at IS NULL AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$new_referrals = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Commission generated (sum of withdrawals created)
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM withdrawal WHERE created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_generated = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
// Commission paid
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM withdrawal WHERE status = 'completed' AND processed_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_paid = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
// Commission pending
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM withdrawal WHERE status = 'pending' AND created_at BETWEEN :start AND :end");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_pending = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>
    <?php include $private_includes_path . 'admin_header.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Báo cáo Tổng hợp</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
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
                        <div class="flex justify-between"><span>Tổng số đăng ký:</span> <strong class="font-medium"><?php echo $total_registrations; ?></strong></div>
                        <div class="flex justify-between"><span>Đăng ký mới (kỳ BC):</span> <strong class="font-medium"><?php echo $new_registrations; ?></strong></div>
                        <div class="flex justify-between"><span>Tài khoản hoạt động:</span> <strong class="font-medium"><?php echo $active_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>Tài khoản bị khóa:</span> <strong class="font-medium"><?php echo $locked_accounts; ?></strong></div>
                    </div>
                </div>

                <!-- Report Card: Tài khoản đo đạc -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-ruler-combined text-primary-600"></i> Tài khoản đo đạc</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng số TK đang HĐ:</span> <strong class="font-medium"><?php echo $active_survey_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK kích hoạt mới (kỳ BC):</span> <strong class="font-medium"><?php echo $new_active_survey_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK sắp hết hạn (30 ngày):</span> <strong class="font-medium"><?php echo $expiring_accounts; ?></strong></div>
                        <div class="flex justify-between"><span>TK đã hết hạn (kỳ BC):</span> <strong class="font-medium"><?php echo $expired_accounts; ?></strong></div>
                    </div>
                </div>

                <!-- Report Card: Giao dịch -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-yellow-600"></i> Giao dịch</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Tổng doanh số (kỳ BC):</span> <strong class="font-medium"><?php echo number_format($total_sales, 0, ',', '.'); ?>đ</strong></div>
                        <div class="flex justify-between"><span>Số GD thành công:</span> <strong class="font-medium"><?php echo $completed_transactions; ?></strong></div>
                        <div class="flex justify-between"><span>Số GD chờ duyệt:</span> <strong class="font-medium"><?php echo $pending_transactions; ?></strong></div>
                        <div class="flex justify-between"><span>Số GD bị từ chối:</span> <strong class="font-medium"><?php echo $failed_transactions; ?></strong></div>
                    </div>
                </div>

                <!-- Report Card: Giới thiệu -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-network-wired text-indigo-600"></i> Giới thiệu</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Lượt giới thiệu mới (kỳ BC):</span> <strong class="font-medium"><?php echo $new_referrals; ?></strong></div>
                        <div class="flex justify-between"><span>Hoa hồng phát sinh (kỳ BC):</span> <strong class="font-medium"><?php echo number_format($commission_generated, 0, ',', '.'); ?>đ</strong></div>
                        <div class="flex justify-between"><span>Hoa hồng đã thanh toán (kỳ BC):</span> <strong class="font-medium"><?php echo number_format($commission_paid, 0, ',', '.'); ?>đ</strong></div>
                        <div class="flex justify-between"><span>Tổng HH chờ thanh toán:</span> <strong class="font-medium"><?php echo number_format($commission_pending, 0, ',', '.'); ?>đ</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/components/buttons.css">

<script>
    document.getElementById('report-filter-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const startDate = document.getElementById('report-start-date').value;
        const endDate = document.getElementById('report-end-date').value;
        const urlParams = new URLSearchParams({ start_date: startDate, end_date: endDate });
        window.location.search = urlParams.toString();
    });
</script>
<?php
include $private_includes_path . 'admin_footer.php';
?>