<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

// Replace manual session and path setup with bootstrap
$bootstrap_data = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$db               = $bootstrap_data['db'];
$base_path        = $bootstrap_data['base_path'];
$user_display_name= $bootstrap_data['user_display_name'];
$private_includes_path = $bootstrap_data['private_includes_path'];

require_once __DIR__ . '/../../../private/actions/invoice/fetch_transactions.php';
// Include new action for revenue sums
require_once __DIR__ . '/../../../private/actions/invoice/get_revenue_sums.php';

// Lấy params phân trang & filter
$current_page  = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 15;
$filters = [
    'search'    => trim($_GET['search'] ?? ''),
    'status'    => trim($_GET['status'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to'   => trim($_GET['date_to'] ?? ''),
];

// Lấy dữ liệu giao dịch & phân trang
$data = fetch_admin_transactions($filters, $current_page, $per_page);
$transactions = $data['transactions'];
$total_items  = $data['total_count'];
$total_pages  = $data['total_pages'];
$current_page = $data['current_page'];
$pagination_base = '?' . http_build_query(array_filter($filters));

// Get total and successful revenue using private action
list($total_revenue, $successful_revenue) = get_revenue_sums($filters);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Giao dịch - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-container { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
        .stats-box {
            flex: 1;
            background: white;
            border-radius: var(--rounded-lg);
            padding: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        .stats-box .icon { font-size: 1.75rem; color: var(--info-600); margin-right: 0.75rem; }
        .stats-box .label { display: block; font-size: var(--font-size-sm); color: var(--gray-600); }
        .stats-box .value { font-size: 1.25rem; font-weight: var(--font-semibold); color: var(--gray-800); }
    </style>
</head>
<body>
    <?php include $private_includes_path . 'admin_header.php'; ?>
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Quản lý Doanh thu</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div class="content-section">
            <h3>Doanh thu</h3>
            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" placeholder="Từ ngày">
                    <input type="date" name="date_to"   value="<?php echo htmlspecialchars($filters['date_to']); ?>"   placeholder="Đến ngày">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </form>

            <div class="stats-container">
                <div class="stats-box">
                    <i class="fas fa-coins icon"></i>
                    <div>
                        <span class="label">Tổng doanh thu</span>
                        <span class="value"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
                <div class="stats-box">
                    <i class="fas fa-check-circle icon"></i>
                    <div>
                        <span class="label">Tổng doanh thu thành công</span>
                        <span class="value"><?php echo number_format($successful_revenue, 0, ',', '.'); ?> đ</span>
                    </div>
                </div>
            </div>

            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Email</th>
                            <th>Gói</th>
                            <th>Số tiền</th>
                            <th>Ngày YC</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="6" style="text-align:center;">Không có giao dịch.</td></tr>
                        <?php else: foreach ($transactions as $tx): ?>
                            <?php
                                $status = htmlspecialchars($tx['registration_status']);
                                // Việt hóa trạng thái
                                switch ($status) {
                                    case 'active':
                                        $text = 'Thành công';
                                        break;
                                    case 'pending':
                                        $text = 'Đang chờ';
                                        break;
                                    case 'rejected':
                                        $text = 'Bị từ chối';
                                        break;
                                    default:
                                        $text = ucfirst($status);
                                }
                                $cls = $status === 'active' ? 'status-approved'
                                      : ($status === 'pending' ? 'status-pending'
                                      : ($status === 'rejected' ? 'status-rejected' : 'status-unknown'));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tx['registration_id']); ?></td>
                                <td><?php echo htmlspecialchars($tx['user_email'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($tx['package_name']); ?></td>
                                <td><?php echo number_format($tx['amount'], 0, ',', '.'); ?> đ</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($tx['request_date'])); ?></td>
                                <td><span class="status-badge <?php echo $cls; ?>"><?php echo $text; ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination-footer">
                <div class="pagination-controls">
                    <button <?php if($current_page<=1) echo 'disabled'; ?> onclick="location.href='<?php echo $pagination_base; ?>&page=<?php echo $current_page-1;?>'">Tr</button>
                    <?php for($i=1;$i<=$total_pages;$i++): ?>
                        <button class="<?php echo $i==$current_page?'active':''; ?>"
                            onclick="location.href='<?php echo $pagination_base; ?>&page=<?php echo $i;?>'">
                            <?php echo $i;?>
                        </button>
                    <?php endfor; ?>
                    <button <?php if($current_page>=$total_pages) echo 'disabled'; ?> onclick="location.href='<?php echo $pagination_base; ?>&page=<?php echo $current_page+1;?>'">Sau</button>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>