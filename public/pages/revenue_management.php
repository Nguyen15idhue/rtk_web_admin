<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

// Replace manual session and path setup with bootstrap
$bootstrap_data = require_once __DIR__ . '/../../private/includes/page_bootstrap.php';
$db               = $bootstrap_data['db'];
$base_path        = $bootstrap_data['base_path'];
$user_display_name= $bootstrap_data['user_display_name'];
$private_includes_path = $bootstrap_data['private_includes_path'];

require_once __DIR__ . '/../../private/actions/invoice/fetch_transactions.php';

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

// Tính tổng doanh thu trong khoảng
try {
    $sumSql = "
        SELECT SUM(r.total_price) 
        FROM registration r
        WHERE r.deleted_at IS NULL
        " . (!empty($filters['date_from']) ? "AND DATE(r.created_at) >= :df " : '') . "
        " . (!empty($filters['date_to'])   ? "AND DATE(r.created_at) <= :dt " : '');
    $stmt = $db->prepare($sumSql);
    if (!empty($filters['date_from'])) $stmt->bindValue(':df', $filters['date_from']);
    if (!empty($filters['date_to']))   $stmt->bindValue(':dt', $filters['date_to']);
    $stmt->execute();
    $total_revenue = (float)$stmt->fetchColumn();
} catch (Exception $e) {
    $total_revenue = 0;
}

// Add calculation of total for successful (active) transactions
try {
    $sumSuccessSql = "
        SELECT SUM(r.total_price)
        FROM registration r
        WHERE r.deleted_at IS NULL
          AND LOWER(r.status) = 'active'
        " . (!empty($filters['date_from']) ? "AND DATE(r.created_at) >= :df " : '') . "
        " . (!empty($filters['date_to'])   ? "AND DATE(r.created_at) <= :dt " : '');
    $stmt2 = $db->prepare($sumSuccessSql);
    if (!empty($filters['date_from'])) $stmt2->bindValue(':df', $filters['date_from']);
    if (!empty($filters['date_to']))   $stmt2->bindValue(':dt', $filters['date_to']);
    $stmt2->execute();
    $successful_revenue = (float)$stmt2->fetchColumn();
} catch (Exception $e) {
    $successful_revenue = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Giao dịch - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-500: #3b82f6; --primary-600: #2563eb; --primary-700: #1d4ed8;
            --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb; --gray-300: #d1d5db;
            --gray-400: #9ca3af; --gray-500: #6b7280; --gray-600: #4b5563; --gray-700: #374151;
            --gray-800: #1f2937; --gray-900: #111827;
            --success-500: #10b981; --success-600: #059669;
            --danger-500: #ef4444; --danger-600: #dc2626;
            --warning-500: #f59e0b;
            --info-600: #0ea5e9;
            --badge-green-bg: #ecfdf5; --badge-green-text: #065f46;
            --badge-red-bg: #fef2f2; --badge-red-text: #991b1b;
            --badge-yellow-bg: #fffbeb; --badge-yellow-text: #b45309; --badge-yellow-border: #fde68a;
            --rounded-md: 0.375rem; --rounded-lg: 0.5rem; --rounded-full: 9999px;
            --font-size-xs: 0.75rem; --font-size-sm: 0.875rem; --font-size-base: 1rem; --font-size-lg: 1.125rem;
            --font-medium: 500; --font-semibold: 600;
            --border-color: var(--gray-200);
        }
        body { font-family: sans-serif; background-color: var(--gray-100); color: var(--gray-800); }
        .content-wrapper { flex-grow: 1; padding: 1.5rem; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem 1.5rem; background: white; border-radius: var(--rounded-lg); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-header h2 { font-size: 1.5rem; font-weight: var(--font-semibold); color: var(--gray-800); }
        .user-info { display: flex; align-items: center; gap: 1rem; font-size: var(--font-size-sm); }
        .user-info span .highlight { color: var(--primary-600); font-weight: var(--font-semibold); }
        .user-info a { color: var(--primary-600); text-decoration: none; }
        .user-info a:hover { text-decoration: underline; }
        .content-section { background: white; border-radius: var(--rounded-lg); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-section h3 { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--gray-700); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; }
        .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
        .filter-bar input, .filter-bar select { padding: 0.6rem 0.8rem; border: 1px solid var(--gray-300); border-radius: var(--rounded-md); font-size: var(--font-size-sm); }
        .filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .filter-bar button, .filter-bar a.btn-secondary { padding: 0.6rem 1rem; font-size: var(--font-size-sm); }

        /* Status‑badge color variants */
        .status-approved {
            background: var(--badge-green-bg);
            color: var(--badge-green-text);
            border-color: #a7f3d0;
        }
        .status-pending {
            background: var(--badge-yellow-bg);
            color: var(--badge-yellow-text);
            border-color: var(--badge-yellow-border);
        }
        .status-rejected {
            background: var(--badge-red-bg);
            color: var(--badge-red-text);
            border-color: #fecaca;
        }
        .status-unknown {
            background: var(--gray-100);
            color: var(--gray-500);
            border-color: var(--gray-300);
        }

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
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
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
                                $text = ucfirst($status);
                                $cls = $status === 'active' ? 'status-approved'
                                      : ($status==='pending'?'status-pending':($status==='rejected'?'status-rejected':'status-unknown'));
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