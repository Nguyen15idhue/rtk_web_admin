<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\account_management.php

// --- Bootstrap and Initialization ---
// Includes session start, auth check, DB connection, base path, etc.
$bootstrap_data = require_once __DIR__ . '/../../private/includes/page_bootstrap.php';
$db = $bootstrap_data['db'];
$base_path = $bootstrap_data['base_path'];
$user_display_name = $bootstrap_data['user_display_name'];
// Note: $private_includes_path now points to the 'includes' directory itself
$private_includes_path = $bootstrap_data['private_includes_path'];

// --- Include Page-Specific Logic ---
// Handles filtering, pagination, and data fetching for accounts
$account_list_data = require __DIR__ . '/../../private/actions/account/handle_account_list.php';
$filters = $account_list_data['filters'];
$accounts = $account_list_data['accounts'];
$total_items = $account_list_data['total_items'];
$total_pages = $account_list_data['total_pages'];
$current_page = $account_list_data['current_page'];
$items_per_page = $account_list_data['items_per_page'];
$pagination_base_url = $account_list_data['pagination_base_url'];

// --- Include Helpers needed for the View ---
// dashboard_helpers contains functions like get_account_status_badge, get_account_action_buttons
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
// functions.php was included in bootstrap, but ensure format_date is available
// If format_date is in functions.php, it's already included.

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý TK Đo đạc - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-500: #3b82f6; --primary-600: #2563eb; --primary-700: #1d4ed8;
            --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb; --gray-300: #d1d5db;
            --gray-400: #9ca3af; --gray-500: #6b7280; --gray-600: #4b5563; --gray-700: #374151;
            --gray-800: #1f2937; --gray-900: #111827;
            --success-500: #10b981; --success-600: #059669; --success-700: #047857;
            --danger-500: #ef4444; --danger-600: #dc2626; --danger-700: #b91c1c;
            --warning-500: #f59e0b; --warning-600: #d97706;
            --info-500: #0ea5e9; --info-600: #0284c7;
            --badge-green-bg: #ecfdf5; --badge-green-text: #065f46;
            --badge-red-bg: #fef2f2; --badge-red-text: #991b1b;
            --badge-yellow-bg: #fffbeb; --badge-yellow-text: #b45309; --badge-yellow-border: #fde68a;
            --badge-gray-bg: #f3f4f6; --badge-gray-text: #374151; --badge-gray-border: #d1d5db;
            --rounded-md: 0.375rem; --rounded-lg: 0.5rem; --rounded-full: 9999px;
            --font-size-xs: 0.75rem; --font-size-sm: 0.875rem; --font-size-base: 1rem; --font-size-lg: 1.125rem;
            --font-medium: 500; --font-semibold: 600;
            --border-color: var(--gray-200);
            --transition-speed: 150ms;
        }
        body { font-family: sans-serif; background-color: var(--gray-100); color: var(--gray-800); }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
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
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border: 1px solid transparent;
            border-radius: var(--rounded-md);
            font-size: var(--font-size-sm);
            font-weight: var(--font-medium);
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: background-color var(--transition-speed) ease-in-out, border-color var(--transition-speed) ease-in-out, color var(--transition-speed) ease-in-out, box-shadow var(--transition-speed) ease-in-out;
            white-space: nowrap;
        }
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn i {
            line-height: 1;
        }
        .btn-primary {
            background-color: var(--primary-600);
            color: white;
            border-color: var(--primary-600);
        }
        .btn-primary:hover {
            background-color: var(--primary-700);
            border-color: var(--primary-700);
        }
        .btn-secondary {
            background-color: white;
            color: var(--gray-700);
            border-color: var(--gray-300);
        }
        .btn-secondary:hover {
            background-color: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-800);
        }
        .btn-success {
            background-color: var(--success-600);
            color: white;
            border-color: var(--success-600);
        }
        .btn-success:hover {
            background-color: var(--success-700);
            border-color: var(--success-700);
        }
        .btn-danger {
            background-color: var(--danger-600);
            color: white;
            border-color: var(--danger-600);
        }
        .btn-danger:hover {
            background-color: var(--danger-700);
            border-color: var(--danger-700);
        }
        .transactions-table-wrapper { overflow-x: auto; background: white; border-radius: var(--rounded-lg); border: 1px solid var(--border-color); box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-top: 1rem; }
        .transactions-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .transactions-table th, .transactions-table td { padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); font-size: var(--font-size-sm); vertical-align: middle; }
        .transactions-table th { background-color: var(--gray-50); font-weight: var(--font-semibold); color: var(--gray-600); white-space: nowrap; }
        .transactions-table tr:last-child td { border-bottom: none; }
        .transactions-table tr:hover { background-color: var(--gray-50); }
        .transactions-table td.status { text-align: center; }
        .transactions-table td.actions { text-align: center; }
        .transactions-table td .action-buttons { display: inline-flex; gap: 0.5rem; justify-content: center; }
        .transactions-table td .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.4rem;
            font-size: 1.1rem;
            line-height: 1;
            border-radius: var(--rounded-full);
            color: var(--gray-500);
            transition: background-color var(--transition-speed) ease-in-out, color var(--transition-speed) ease-in-out;
        }
        .transactions-table td .btn-icon:hover {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }
        .transactions-table td .btn-view:hover { color: var(--info-600); background-color: rgba(14, 165, 233, 0.1); }
        .transactions-table td .btn-edit:hover { color: var(--warning-600); background-color: rgba(245, 158, 11, 0.1); }
        .transactions-table td .btn-danger:hover { color: var(--danger-700); background-color: rgba(220, 38, 38, 0.1); }
        .transactions-table td .btn-toggle-on:hover { color: var(--success-700); background-color: rgba(5, 150, 105, 0.1); }
        .transactions-table td .btn-toggle-off:hover { color: var(--danger-700); background-color: rgba(220, 38, 38, 0.1); }

        .status-badge { padding: 0.3rem 0.8rem; border-radius: var(--rounded-full); font-size: 0.8rem; display: inline-block; font-weight: var(--font-medium); text-align: center; min-width: 90px; border: 1px solid transparent; }
        .status-badge.badge-green { background-color: var(--badge-green-bg); color: var(--badge-green-text); border-color: var(--success-500); }
        .status-badge.badge-yellow { background-color: var(--badge-yellow-bg); color: var(--badge-yellow-text); border-color: var(--badge-yellow-border); }
        .status-badge.badge-red { background-color: var(--badge-red-bg); color: var(--badge-red-text); border-color: var(--danger-500); }
        .status-badge.badge-gray { background-color: var(--badge-gray-bg); color: var(--badge-gray-text); border-color: var(--badge-gray-border); }

        .pagination-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: var(--font-size-sm); color: var(--gray-600); }
        .pagination-controls { display: flex; gap: 0.3rem; }
        .pagination-controls button, .pagination-controls span { padding: 0.4rem 0.8rem; border: 1px solid var(--gray-300); background-color: #fff; border-radius: var(--rounded-md); font-size: var(--font-size-sm); display: inline-flex; align-items: center; justify-content: center; min-width: 32px; }
        .pagination-controls button { cursor: pointer; }
        .pagination-controls button:disabled { background-color: var(--gray-100); color: var(--gray-400); cursor: not-allowed; }
        .pagination-controls button.active { background-color: var(--primary-500); color: #fff; border-color: var(--primary-500); font-weight: bold; }
        .pagination-controls span { background-color: transparent; border: none; color: var(--gray-500); }

        #no-results-row td { text-align: center; padding: 3rem; color: var(--gray-500); font-size: var(--font-size-base); }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
        .header-actions h3 { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
        .header-actions .btn-primary { font-size: var(--font-size-sm); }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 25px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: var(--rounded-lg); position: relative; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19); }
        .modal-header { padding-bottom: 15px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h4 { margin: 0; font-size: 1.25rem; font-weight: var(--font-semibold); color: var(--gray-800); }
        .modal-close { color: var(--gray-400); font-size: 1.5rem; font-weight: bold; cursor: pointer; border: none; background: none; line-height: 1; padding: 0.5rem; border-radius: var(--rounded-full); transition: color var(--transition-speed) ease-in-out, background-color var(--transition-speed) ease-in-out; }
        .modal-close:hover, .modal-close:focus { color: var(--gray-700); background-color: var(--gray-100); text-decoration: none; outline: none; }
        .modal-body { margin-bottom: 20px; }
        .modal-body .detail-row { display: flex; margin-bottom: 10px; font-size: var(--font-size-sm); }
        .modal-body .detail-label { font-weight: var(--font-semibold); color: var(--gray-600); width: 150px; flex-shrink: 0; }
        .modal-body .detail-value { color: var(--gray-800); white-space: pre-wrap; word-break: break-word; }
        .modal-footer { padding-top: 15px; border-top: 1px solid var(--border-color); text-align: right; }
        .modal-footer .btn { margin-left: 0.5rem; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: var(--font-medium); color: var(--gray-700); font-size: var(--font-size-sm); }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--rounded-md);
            font-size: var(--font-size-sm);
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .form-group .error-message {
            color: var(--danger-600);
            font-size: var(--font-size-sm);
            margin-top: 0.5rem;
            text-align: left;
        }

        /* Toast styles */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        .toast {
            background-color: var(--gray-800);
            color: #fff;
            padding: 12px 20px;
            border-radius: var(--rounded-md);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.5s ease, transform 0.5s ease;
            min-width: 250px;
            text-align: left;
            font-size: var(--font-size-sm);
        }
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        .toast-success { background-color: var(--success-600); }
        .toast-error { background-color: var(--danger-600); }
        .toast-warning { background-color: var(--warning-500); color: var(--gray-900); }
        .toast-info { background-color: var(--info-500); }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php
        // Use the path returned by bootstrap
        include $private_includes_path . 'admin_sidebar.php';
    ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Quản lý TK Đo đạc</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo $user_display_name; // Already HTML-escaped in bootstrap ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-account-management" class="content-section">
            <div class="header-actions">
                <h3>Danh sách tài khoản đo đạc</h3>
                <button class="btn btn-primary" onclick="openCreateMeasurementAccountModal()" data-permission="account_create">
                    <i class="fas fa-plus"></i> Tạo TK thủ công
                </button>
            </div>
            <p class="text-xs sm:text-sm text-gray-600 mb-4" style="font-size: var(--font-size-sm); color: var(--gray-600); margin-bottom: 1rem;">Quản lý các tài khoản dịch vụ đo đạc RTK của khách hàng.</p>

            <!-- Filter Form -->
            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="search" placeholder="Tìm ID TK, Username, Email..." name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                    <select name="package">
                        <option value="">Tất cả gói</option>
                        <?php
                        $package_options = ["Gói 1 Tháng", "Gói 3 Tháng", "Gói 6 Tháng", "Gói 1 Năm", "Gói Vĩnh Viễn"];
                        foreach ($package_options as $pkg_name) {
                            $selected = (($filters['package'] ?? '') == $pkg_name) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($pkg_name) . "\" $selected>" . htmlspecialchars($pkg_name) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?php echo (($filters['status'] ?? '') == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="pending" <?php echo (($filters['status'] ?? '') == 'pending') ? 'selected' : ''; ?>>Chờ KH</option>
                        <option value="expired" <?php echo (($filters['status'] ?? '') == 'expired') ? 'selected' : ''; ?>>Hết hạn</option>
                        <option value="suspended" <?php echo (($filters['status'] ?? '') == 'suspended') ? 'selected' : ''; ?>>Đình chỉ</option>
                        <option value="rejected" <?php echo (($filters['status'] ?? '') == 'rejected') ? 'selected' : ''; ?>>Bị từ chối</option>
                    </select>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </form>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="accountsTable">
                    <thead>
                        <tr>
                            <th>ID TK</th>
                            <th>Username TK</th>
                            <th>Email user</th>
                            <th>Gói</th>
                            <th>Ngày KH</th>
                            <th>Ngày HH</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th class="actions" style="text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($accounts)): ?>
                            <?php foreach ($accounts as $account): ?>
                                <tr data-account-id="<?php echo htmlspecialchars($account['id']); ?>" data-status="<?php echo htmlspecialchars($account['derived_status']); ?>">
                                    <td><?php echo htmlspecialchars($account['id']); ?></td>
                                    <td><?php echo htmlspecialchars($account['username_acc'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($account['user_email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($account['package_name'] ?? ''); ?></td>
                                    <td><?php echo format_date($account['activation_date'] ?? null); ?></td>
                                    <td><?php echo format_date($account['expiry_date'] ?? null); ?></td>
                                    <td class="status"><?php echo get_account_status_badge($account['derived_status'] ?? 'unknown'); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <?php echo get_account_action_buttons($account); // Assumes this function is in dashboard_helpers.php and generates buttons with btn-icon class ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="8">Không tìm thấy tài khoản phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-footer">
                <div class="pagination-info">
                    <?php if ($total_items > 0):
                        $start_item = ($current_page - 1) * $items_per_page + 1;
                        $end_item = min($start_item + $items_per_page - 1, $total_items);
                    ?>
                        Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> tài khoản
                    <?php else: ?>
                        Không có tài khoản nào
                    <?php endif; ?>
                </div>
                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                    <?php
                        $max_pages_to_show = 5;
                        $start_page = max(1, $current_page - floor($max_pages_to_show / 2));
                        $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
                        if ($end_page - $start_page + 1 < $max_pages_to_show) {
                            $start_page = max(1, $end_page - $max_pages_to_show + 1);
                        }

                        if ($start_page > 1) {
                            echo '<button onclick="window.location.href=\'' . $pagination_base_url . 'page=1\'">1</button>';
                            if ($start_page > 2) echo '<span>...</span>';
                        }

                        for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . $i; ?>'"><?php echo $i; ?></button>
                    <?php
                        endfor;

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) echo '<span>...</span>';
                            echo '<button onclick="window.location.href=\'' . $pagination_base_url . 'page=' . $total_pages . '\'">' . $total_pages . '</button>';
                        }
                    ?>
                    <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- View Account Modal -->
<div id="viewAccountModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Chi tiết tài khoản</h4>
            <span class="modal-close" onclick="closeModal('viewAccountModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewAccountDetailsContent">
            <p>Đang tải...</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('viewAccountModal')">Đóng</button>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div id="createAccountModal" class="modal">
    <div class="modal-content">
        <form id="createAccountForm">
            <div class="modal-header">
                <h4>Tạo tài khoản đo đạc mới</h4>
                <span class="modal-close" onclick="closeModal('createAccountModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="create-username">Username TK:</label>
                    <input type="text" id="create-username" name="username_acc" required>
                </div>
                <div class="form-group">
                    <label for="create-password">Mật khẩu TK:</label>
                    <input type="password" id="create-password" name="password_acc" required>
                </div>
                <div class="form-group">
                    <label for="create-user-email">Email User:</label>
                    <input type="email" id="create-user-email" name="user_email" required>
                </div>
                <div class="form-group">
                    <label for="create-package">Gói:</label>
                    <select id="create-package" name="package_name" required>
                        <option value="">Chọn gói</option>
                        <option value="Gói 1 Tháng">Gói 1 Tháng</option>
                        <option value="Gói 3 Tháng">Gói 3 Tháng</option>
                        <option value="Gói 6 Tháng">Gói 6 Tháng</option>
                        <option value="Gói 1 Năm">Gói 1 Năm</option>
                        <option value="Gói Vĩnh Viễn">Gói Vĩnh Viễn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create-activation-date">Ngày kích hoạt:</label>
                    <input type="date" id="create-activation-date" name="activation_date">
                </div>
                <div class="form-group">
                    <label for="create-expiry-date">Ngày hết hạn:</label>
                    <input type="date" id="create-expiry-date" name="expiry_date">
                </div>
                <div class="form-group">
                    <label for="create-status">Trạng thái:</label>
                    <select id="create-status" name="status">
                        <option value="active">Hoạt động</option>
                        <option value="pending">Chờ KH</option>
                        <option value="suspended">Đình chỉ</option>
                    </select>
                </div>
                 <div class="form-group error-message" id="createAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createAccountModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Account Modal -->
<div id="editAccountModal" class="modal">
    <div class="modal-content">
        <form id="editAccountForm">
            <div class="modal-header">
                <h4>Chỉnh sửa tài khoản</h4>
                <span class="modal-close" onclick="closeModal('editAccountModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-account-id" name="id">
                <div class="form-group">
                    <label for="edit-username">Username TK:</label>
                    <input type="text" id="edit-username" name="username_acc" required>
                </div>
                <div class="form-group">
                    <label for="edit-password">Mật khẩu TK (Để trống nếu không đổi):</label>
                    <input type="password" id="edit-password" name="password_acc">
                </div>
                <div class="form-group">
                    <label for="edit-user-email">Email User:</label>
                    <input type="email" id="edit-user-email" name="user_email" required>
                </div>
                <div class="form-group">
                    <label for="edit-package">Gói:</label>
                    <select id="edit-package" name="package_name" required>
                        <option value="">Chọn gói</option>
                        <option value="Gói 1 Tháng">Gói 1 Tháng</option>
                        <option value="Gói 3 Tháng">Gói 3 Tháng</option>
                        <option value="Gói 6 Tháng">Gói 6 Tháng</option>
                        <option value="Gói 1 Năm">Gói 1 Năm</option>
                        <option value="Gói Vĩnh Viễn">Gói Vĩnh Viễn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-activation-date">Ngày kích hoạt:</label>
                    <input type="date" id="edit-activation-date" name="activation_date">
                </div>
                <div class="form-group">
                    <label for="edit-expiry-date">Ngày hết hạn:</label>
                    <input type="date" id="edit-expiry-date" name="expiry_date">
                </div>
                <div class="form-group">
                    <label for="edit-status">Trạng thái:</label>
                    <select id="edit-status" name="status" required>
                        <option value="active">Hoạt động</option>
                        <option value="pending">Chờ KH</option>
                        <option value="expired">Hết hạn</option>
                        <option value="suspended">Đình chỉ</option>
                        <option value="rejected">Bị từ chối</option>
                    </select>
                </div>
                 <div class="form-group error-message" id="editAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editAccountModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div> <!-- Toast container -->

<script>
    // --- API Base Path for JavaScript (relative) ---
    const apiBasePath = '../actions/account/index.php';
    const basePath = '<?php echo $base_path; ?>'; // Needed for some actions potentially

    const accountsTableBody = document.getElementById('accountsTable')?.querySelector('tbody');
    const noResultsRow = document.getElementById('no-results-row');

    // Modal elements
    const viewModal = document.getElementById('viewAccountModal');
    const viewDetailsContent = document.getElementById('viewAccountDetailsContent');
    const createModal = document.getElementById('createAccountModal');
    const editModal = document.getElementById('editAccountModal');
    const createAccountForm = document.getElementById('createAccountForm');
    const editAccountForm = document.getElementById('editAccountForm');
    const createAccountError = document.getElementById('createAccountError');
    const editAccountError = document.getElementById('editAccountError');

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
        // Reset forms and errors when closing
        if (modalId === 'createAccountModal' && createAccountForm) {
            createAccountForm.reset();
            if(createAccountError) createAccountError.textContent = '';
        } else if (modalId === 'editAccountModal' && editAccountForm) {
            editAccountForm.reset();
             if(editAccountError) editAccountError.textContent = '';
        }
    }

    function openCreateMeasurementAccountModal() {
        if (createAccountForm) createAccountForm.reset();
        if (createAccountError) createAccountError.textContent = '';
        if (createModal) createModal.style.display = 'block';
    }

    async function openEditAccountModal(accountId) {
        if (!editModal || !editAccountForm) return;

        editAccountForm.reset();
        if (editAccountError) editAccountError.textContent = '';

        try {
            const response = await fetch(`${apiBasePath}?action=get_account_details&id=${accountId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success && data.account) {
                const account = data.account;
                editAccountForm.querySelector('#edit-account-id').value = account.id;
                editAccountForm.querySelector('#edit-username').value = account.username_acc || '';
                editAccountForm.querySelector('#edit-user-email').value = account.user_email || '';
                editAccountForm.querySelector('#edit-package').value = account.package_name || '';
                // Ensure date format is YYYY-MM-DD for input type="date"
                editAccountForm.querySelector('#edit-activation-date').value = account.activation_date ? account.activation_date.split(' ')[0] : '';
                editAccountForm.querySelector('#edit-expiry-date').value = account.expiry_date ? account.expiry_date.split(' ')[0] : '';
                editAccountForm.querySelector('#edit-status').value = account.derived_status || 'unknown'; // Use derived_status

                editModal.style.display = 'block';
            } else {
                showToast('error', data.message || 'Không thể tải chi tiết tài khoản.');
            }
        } catch (error) {
            console.error('Error fetching account details:', error);
            showToast('error', 'Lỗi khi tải chi tiết tài khoản.');
        }
    }

    function viewAccountDetails(accountId) {
        if (!viewModal || !viewDetailsContent) {
            console.error('View modal elements not found');
            alert('Lỗi giao diện: Không tìm thấy cửa sổ chi tiết.');
            return;
        }
        viewDetailsContent.innerHTML = '<p>Đang tải...</p>';
        viewModal.style.display = 'block';

        const url = `${apiBasePath}?action=get_account_details&id=${accountId}`;
        console.log("Attempting to fetch URL:", url);

        fetch(url)
            .then(response => {
                console.log("Received response status:", response.status, "Status text:", response.statusText);
                const contentType = response.headers.get("content-type");
                console.log("Received content-type:", contentType);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error("Server response text (if any):", text);
                        throw new Error(`HTTP error! status: ${response.status}, Status Text: ${response.statusText}. Check Network tab for details. Response snippet: ${text.substring(0, 200)}`);
                    }).catch(textError => {
                         console.error("Could not read response text:", textError);
                         throw new Error(`HTTP error! status: ${response.status}, Status Text: ${response.statusText}. Could not read response body.`);
                    });
                }

                if (!contentType || !contentType.includes("application/json")) {
                     return response.text().then(text => {
                        console.error("Non-JSON response received:", text);
                        throw new TypeError(`Oops, we haven't got JSON! Content-Type: ${contentType}. Response snippet: ${text.substring(0, 200)}`);
                    });
                }

                return response.json();
            })
            .then(result => {
                 if (result.success && result.account) {
                    const account = result.account;
                    // Use detail-row, detail-label, detail-value classes for styling
                    let detailsHtml = `
                        <div class="detail-row"><span class="detail-label">ID TK:</span> <span class="detail-value">${account.id || 'N/A'}</span></div>
                        <div class="detail-row"><span class="detail-label">Username TK:</span> <span class="detail-value">${account.username_acc || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Email User:</span> <span class="detail-value">${account.user_email || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Gói:</span> <span class="detail-value">${account.package_name || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Ngày KH:</span> <span class="detail-value">${account.activation_date_formatted || account.activation_date || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Ngày HH:</span> <span class="detail-value">${account.expiry_date_formatted || account.expiry_date || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${account.status_text || account.derived_status || account.status || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${account.created_at_formatted || account.created_at || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Cập nhật:</span> <span class="detail-value">${account.updated_at_formatted || account.updated_at || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Ghi chú:</span> <span class="detail-value">${account.notes || '-'}</span></div>
                        `;
                    viewDetailsContent.innerHTML = detailsHtml;
                } else {
                     viewDetailsContent.innerHTML = `<p style="color: red;">Lỗi: ${result.message || 'Không thể tải thông tin tài khoản. Phản hồi không thành công.'}</p>`;
                }
            })
            .catch(error => {
                console.error('Detailed error fetching account details for view:', error);
                viewDetailsContent.innerHTML = `<p style="color: red;">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}. Kiểm tra Console (F12) và Network tab để biết thêm chi tiết.</p>`;
            });
    }

    // Function to generate status badge HTML (mirrors PHP helper)
    function get_account_status_badge_js(status) {
        status = status ? status.toLowerCase() : 'unknown';
        let badgeClass = 'badge-gray';
        let statusText = 'Không xác định';

        switch (status) {
            case 'active': badgeClass = 'badge-green'; statusText = 'Hoạt động'; break;
            case 'pending': badgeClass = 'badge-yellow'; statusText = 'Chờ KH'; break;
            case 'expired': badgeClass = 'badge-red'; statusText = 'Hết hạn'; break;
            case 'suspended': badgeClass = 'badge-gray'; statusText = 'Đình chỉ'; break;
            case 'rejected': badgeClass = 'badge-red'; statusText = 'Bị từ chối'; break;
        }
        return `<span class="status-badge ${badgeClass}">${statusText}</span>`;
    }

    // Attach submit listeners
    if (createAccountForm) {
        createAccountForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleCreateAccountSubmit(this);
        });
    } else {
        console.error("Element with ID 'createAccountForm' not found.");
    }

    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleEditAccountSubmit(this);
        });
    } else {
        console.error("Element with ID 'editAccountForm' not found.");
    }

    async function handleCreateAccountSubmit(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang tạo...';
        if (createAccountError) createAccountError.textContent = '';

        try {
            const response = await fetch(`${apiBasePath}?action=create_account`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showToast('success', result.message || 'Tạo tài khoản thành công!');
                closeModal('createAccountModal');
                window.location.reload(); // Reload to see the new account
            } else {
                if(createAccountError) createAccountError.textContent = result.message || 'Tạo tài khoản thất bại.';
                showToast('error', result.message || 'Tạo tài khoản thất bại.');
            }
        } catch (error) {
            console.error('Error creating account:', error);
             if(createAccountError) createAccountError.textContent = 'Lỗi khi gửi yêu cầu tạo tài khoản.';
            showToast('error', 'Lỗi khi gửi yêu cầu tạo tài khoản.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Tạo tài khoản';
        }
    }

    async function handleEditAccountSubmit(form) {
        const formData = new FormData(form);
        const accountId = formData.get('id');
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang lưu...';
         if (editAccountError) editAccountError.textContent = '';

        try {
            const response = await fetch(`${apiBasePath}?action=update_account`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showToast('success', result.message || 'Cập nhật tài khoản thành công!');
                closeModal('editAccountModal');
                // Update the table row directly instead of full reload for better UX
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    // Fallback to reload if updated data isn't returned
                    window.location.reload();
                }
            } else {
                if(editAccountError) editAccountError.textContent = result.message || 'Cập nhật tài khoản thất bại.';
                showToast('error', result.message || 'Cập nhật tài khoản thất bại.');
            }
        } catch (error) {
            console.error('Error updating account:', error);
            if(editAccountError) editAccountError.textContent = 'Lỗi khi gửi yêu cầu cập nhật.';
            showToast('error', 'Lỗi khi gửi yêu cầu cập nhật.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Lưu thay đổi';
        }
    }

    function updateTableRow(accountId, updatedData) {
        const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
        if (!row) return;

        const formatDateCell = (dateString) => {
            if (!dateString) return '-';
            try {
                // Assuming format_date PHP function returns DD/MM/YYYY
                // If the API returns YYYY-MM-DD, we might need to reformat here
                // For simplicity, let's assume the API returns a pre-formatted string or null
                 return dateString.split(' ')[0].split('-').reverse().join('/'); // Basic YYYY-MM-DD to DD/MM/YYYY
            } catch (e) {
                return '-';
            }
        };

        // Update cell content based on updatedData
        row.cells[1].textContent = updatedData.username_acc || '';
        row.cells[2].textContent = updatedData.user_email || '';
        row.cells[3].textContent = updatedData.package_name || '';
        row.cells[4].textContent = formatDateCell(updatedData.activation_date);
        row.cells[5].textContent = formatDateCell(updatedData.expiry_date);
        row.cells[6].innerHTML = get_account_status_badge_js(updatedData.derived_status || 'unknown'); // Update status badge
        row.dataset.status = updatedData.derived_status || 'unknown'; // Update data attribute

        // Re-generate action buttons if necessary (requires updatedData to contain all needed fields for get_account_action_buttons logic)
        // This part is complex as it requires replicating the PHP logic in JS or making another API call.
        // For now, we only update data fields. A page reload might be simpler if actions change significantly based on status.
        // Example: If the toggle button needs to change icon/action based on new status
        const actionCell = row.cells[7];
        // Find the toggle button and update its state if possible (simplified example)
        const toggleButton = actionCell.querySelector('button[onclick*="toggleAccountStatus"]');
        if (toggleButton) {
             // Basic update - more complex logic might be needed based on get_account_action_buttons
             const newStatus = updatedData.derived_status;
             const isSuspended = newStatus === 'suspended';
             const newAction = isSuspended ? 'unsuspend' : 'suspend';
             const newIcon = isSuspended ? 'fa-play-circle' : 'fa-pause-circle'; // Example icons
             const newTitle = isSuspended ? 'Bỏ đình chỉ' : 'Đình chỉ';
             toggleButton.title = newTitle;
             toggleButton.setAttribute('onclick', `toggleAccountStatus('${accountId}', '${newAction}', event)`);
             const iconElement = toggleButton.querySelector('i');
             if (iconElement) {
                 iconElement.className = `fas ${newIcon}`;
             }
        }
    }

    async function deleteAccount(accountId, event) {
        event.stopPropagation(); // Prevent triggering row click or other parent events
        if (!confirm(`Bạn có chắc chắn muốn xóa tài khoản ID ${accountId}? Hành động này không thể hoàn tác.`)) {
            return;
        }

        try {
            const response = await fetch(`${apiBasePath}?action=delete_account`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Send as JSON
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: accountId }) // Send ID in JSON body
            });
            const result = await response.json();

            if (result.success) {
                showToast('success', result.message || 'Xóa tài khoản thành công!');
                const row = accountsTableBody?.querySelector(`tr[data-account-id="${accountId}"]`);
                if (row) {
                    row.remove();
                    // Check if table is empty and show 'no results' row
                    if (accountsTableBody && accountsTableBody.rows.length === 0 && noResultsRow) {
                        noResultsRow.style.display = 'table-row';
                    }
                }
            } else {
                showToast('error', result.message || 'Xóa tài khoản thất bại.');
            }
        } catch (error) {
            console.error('Error deleting account:', error);
            showToast('error', 'Lỗi khi gửi yêu cầu xóa.');
        }
    }

    async function toggleAccountStatus(accountId, action, event) {
        event.stopPropagation(); // Prevent triggering row click or other parent events

        let confirmMessage = `Bạn có chắc muốn ${action === 'suspend' ? 'đình chỉ' : action === 'unsuspend' ? 'bỏ đình chỉ' : action === 'approve' ? 'phê duyệt' : 'thực hiện hành động này với'} tài khoản ID ${accountId}?`;
        if (!confirm(confirmMessage)) {
            return;
        }

        try {
            const response = await fetch(`${apiBasePath}?action=toggle_account_status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Send as JSON
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: accountId, action: action }) // Send ID and action in JSON body
            });
            const result = await response.json();

            if (result.success) {
                showToast('success', result.message || 'Cập nhật trạng thái thành công!');
                // Update the table row directly
                if (result.account) {
                    updateTableRow(accountId, result.account);
                } else {
                    // Fallback to reload if updated data isn't returned
                    window.location.reload();
                }
            } else {
                showToast('error', result.message || 'Cập nhật trạng thái thất bại.');
            }
        } catch (error) {
            console.error('Error toggling account status:', error);
            showToast('error', 'Lỗi khi gửi yêu cầu cập nhật trạng thái.');
        }
    }

    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return; // Should exist now

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Trigger reflow to enable transition
        toast.offsetHeight;

        toast.classList.add('show');

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            // Remove the element from DOM after transition ends
            toast.addEventListener('transitionend', () => {
                 if (toast.parentNode === toastContainer) {
                    toastContainer.removeChild(toast);
                 }
            }, { once: true });
        }, 3000);
    }

    // Close modals if clicked outside the content area
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target == modal) {
                closeModal(modal.id);
            }
        });
    }

</script>

</body>
</html>
