<?php
// filepath: public\pages\reports.php

// --- Bootstrap and Initialization ---
$bootstrap_data         = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db                      = $bootstrap_data['db'];
$base_url                = $bootstrap_data['base_url'];
$private_layouts_path   = $bootstrap_data['private_layouts_path'];
$user_display_name       = $bootstrap_data['user_display_name'];

// authorization check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$pdo = $db;

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$start_datetime = $start_date . ' 00:00:00';
$end_datetime = $end_date . ' 23:59:59';

// Total registrations
$stmt = $pdo->query("SELECT COUNT(id) as count FROM user");
$total_registrations = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// New registrations in period
$stmt = $pdo->prepare("
    SELECT COUNT(id) as count
    FROM user
    WHERE deleted_at IS NULL
      AND created_at BETWEEN :start AND :end
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$new_registrations = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Active accounts
$stmt = $pdo->query("SELECT COUNT(id) as count FROM user WHERE deleted_at IS NULL");
$active_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Locked accounts (non-active)
$stmt = $pdo->query("SELECT COUNT(id) as count FROM user WHERE deleted_at IS NOT NULL");
$locked_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

// Active survey accounts
$stmt = $pdo->query("SELECT COUNT(sa.id) as count FROM survey_account sa JOIN registration r ON sa.registration_id = r.id WHERE sa.enabled = 1 AND sa.deleted_at IS NULL AND r.deleted_at IS NULL");
$active_survey_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// New active survey accounts in period
$stmt = $pdo->prepare("
    SELECT COUNT(sa.id) as count
    FROM survey_account sa
    JOIN registration r ON sa.registration_id = r.id
    WHERE sa.deleted_at IS NULL
      AND r.deleted_at IS NULL
      AND sa.created_at BETWEEN :start AND :end
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$new_active_survey_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Accounts expiring in 30 days
$stmt = $pdo->query("
    SELECT COUNT(sa.id) as count
    FROM survey_account sa
    JOIN registration r ON sa.registration_id = r.id
    WHERE sa.end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
      AND sa.deleted_at IS NULL
      AND r.deleted_at IS NULL
");
$expiring_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;
// Accounts expired in period
$stmt = $pdo->prepare("
    SELECT COUNT(sa.id) as count
    FROM survey_account sa
    JOIN registration r ON sa.registration_id = r.id
    WHERE sa.end_time BETWEEN :start AND :end
      AND sa.deleted_at IS NULL
      AND r.deleted_at IS NULL
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$expired_accounts = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['count'] : 0;

// Transactions
// Total sales in period
$stmt = $pdo->prepare("
    SELECT SUM(th.amount) AS total
    FROM transaction_history th
    WHERE th.status = 'completed'
      AND th.created_at BETWEEN :start AND :end
");
$stmt->execute([':start' => $start_datetime, ':end' => $end_datetime]);
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
// Commission generated (sum of withdrawal_request)
$stmt = $pdo->prepare("
    SELECT SUM(amount) as total
    FROM withdrawal_request
    WHERE created_at BETWEEN :start AND :end
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_generated = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
// Commission paid
$stmt = $pdo->prepare("
    SELECT SUM(amount) as total
    FROM withdrawal_request
    WHERE status = 'completed'
      AND updated_at BETWEEN :start AND :end
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_paid = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;
// Commission pending
$stmt = $pdo->prepare("
    SELECT SUM(amount) as total
    FROM withdrawal_request
    WHERE status = 'pending'
      AND created_at BETWEEN :start AND :end
");
$stmt->execute([':start'=>$start_datetime,':end'=>$end_datetime]);
$commission_pending = ($row = $stmt->fetch(PDO::FETCH_ASSOC)) ? $row['total'] : 0;

?>
<?php include $private_layouts_path . 'admin_header.php'; ?>
<?php include $private_layouts_path . 'admin_sidebar.php'; ?>
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
            <form id="report-filter-form" method="GET" action="">
                <div class="filter-bar">
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
                </div>
            </form>
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
                        <div class="flex justify-between"><span>Tổng doanh số (kỳ BC):</span> <strong><?php echo number_format($total_sales,0,',','.'); ?>đ</strong></div>
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
                        <div class="flex justify-between"><span>Hoa hồng phát sinh (kỳ BC):</span> <strong><?php echo number_format($commission_generated,0,',','.'); ?>đ</strong></div>
                        <div class="flex justify-between"><span>Hoa hồng đã thanh toán (kỳ BC):</span> <strong><?php echo number_format($commission_paid,0,',','.'); ?>đ</strong></div>
                        <div class="flex justify-between"><span>Tổng HH chờ thanh toán:</span> <strong><?php echo number_format($commission_pending,0,',','.'); ?>đ</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    document.getElementById('report-filter-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const startDate = document.getElementById('report-start-date').value;
        const endDate = document.getElementById('report-end-date').value;
        const urlParams = new URLSearchParams({ start_date: startDate, end_date: endDate });
        window.location.search = urlParams.toString();
    });
</script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>