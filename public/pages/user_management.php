<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\user_management.php
session_start();
// --- Includes and Setup --- 
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

// --- Base Path Calculation --- 
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
$project_folder_index = array_search('rtk_web_admin', $script_name_parts);
$base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
$base_path = $protocol . $host . $base_path_segment;

// --- Include Required Files --- 
require_once __DIR__ . '/../../private/utils/functions.php'; // General helpers (includes format_date)
require_once __DIR__ . '/../../private/utils/user_helpers.php'; // User-specific helpers (includes get_user_status_display)
require_once __DIR__ . '/../../private/actions/user/fetch_users.php'; // User fetching logic

$user_display_name = $_SESSION['admin_name'] ?? 'Admin';

// --- Get Filters --- 
$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
];

// --- Pagination Setup --- 
$items_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Users --- 
$userData = fetch_paginated_users($filters, $current_page, $items_per_page);
$users = $userData['users'];
$total_items = $userData['total_count'];
$total_pages = $userData['total_pages'];
$current_page = $userData['current_page']; // Use the validated page number from the function

// --- Build Pagination URL --- 
$pagination_params = $filters; // Start with existing filters
unset($pagination_params['page']);
$pagination_base_url = '?' . http_build_query($pagination_params);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
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
        .transactions-table td .btn-secondary i.fa-toggle-off { color: var(--danger-600); }
        .transactions-table td .btn-success i.fa-toggle-on { color: var(--success-600); }
        .transactions-table td .btn-secondary:hover i.fa-toggle-off { color: var(--danger-700); background-color: rgba(220, 38, 38, 0.1); }
        .transactions-table td .btn-success:hover i.fa-toggle-on { color: var(--success-700); background-color: rgba(5, 150, 105, 0.1); }
        .status-badge { padding: 0.3rem 0.8rem; border-radius: var(--rounded-full); font-size: 0.8rem; display: inline-block; font-weight: var(--font-medium); text-align: center; min-width: 90px; border: 1px solid transparent; }
        .status-active { color: var(--success-600); font-weight: bold; }
        .status-inactive { color: var(--danger-600); font-weight: bold; }
        .pagination-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: var(--font-size-sm); color: var(--gray-600); }
        .pagination-controls { display: flex; gap: 0.3rem; }
        .pagination-controls button { padding: 0.4rem 0.8rem; border: 1px solid var(--gray-300); background-color: #fff; cursor: pointer; border-radius: var(--rounded-md); font-size: var(--font-size-sm); }
        .pagination-controls button:disabled { background-color: var(--gray-100); color: var(--gray-400); cursor: not-allowed; }
        .pagination-controls button.active { background-color: var(--primary-500); color: #fff; border-color: var(--primary-500); font-weight: bold; }
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
        .modal-body .detail-value { color: var(--gray-800); }
        .modal-footer { padding-top: 15px; border-top: 1px solid var(--border-color); text-align: right; }
        .modal-footer .btn { margin-left: 0.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: var(--font-medium); color: var(--gray-700); font-size: var(--font-size-sm); }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="tel"] {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--rounded-md);
            font-size: var(--font-size-sm);
            box-sizing: border-box;
        }
        .form-group input:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .form-group .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .form-group .checkbox-group input[type="checkbox"] { margin: 0; }
        #editUserForm .company-fields { display: none; }
        #editUserForm .company-fields.visible { display: block; }
        #createUserForm .company-fields { display: none; }
        #createUserForm .company-fields.visible { display: block; }
        #createUserForm .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        #createUserForm .error-message {
            color: var(--danger-600);
            font-size: var(--font-size-sm);
            margin-top: 1rem;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php include __DIR__ . '/../../private/includes/admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Quản lý Người dùng</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-user-management" class="content-section">
            <div class="header-actions">
                 <h3>Quản lý người dùng (KH)</h3>
                 <button class="btn btn-primary" onclick="openCreateUserModal()" data-permission="user_create">
                    <i class="fas fa-plus"></i> Thêm người dùng
                </button>
            </div>
             <p class="text-xs sm:text-sm text-gray-600 mb-4" style="font-size: var(--font-size-sm); color: var(--gray-600); margin-bottom: 1rem;">Quản lý tài khoản người dùng đăng ký (không phải tài khoản quản trị).</p>

            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="search" placeholder="Tìm Email, Tên..." name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?php echo ($filters['status'] == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="inactive" <?php echo ($filters['status'] == 'inactive') ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                    </select>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-times"></i> Xóa lọc</a>
                </div>
            </form>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Loại TK</th>
                            <th>Tên công ty</th>
                            <th>Mã số thuế</th>
                            <th>Ngày tạo</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th class="actions" style="text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                    <td><?php echo $user['is_company'] ? 'Công ty' : 'Cá nhân'; ?></td>
                                    <td><?php echo $user['is_company'] ? htmlspecialchars($user['company_name'] ?? '-') : '-'; ?></td>
                                    <td><?php echo $user['is_company'] ? htmlspecialchars($user['tax_code'] ?? '-') : '-'; ?></td>
                                    <td><?php echo format_date($user['created_at']); ?></td>
                                    <td><?php echo get_user_status_display($user); ?></td>
                                    <td class="actions">
                                        <div class="action-buttons">
                                            <button class="btn-icon btn-view" title="Xem chi tiết" onclick="viewUserDetails('<?php echo htmlspecialchars($user['id']); ?>')"><i class="fas fa-eye"></i></button>
                                            <button class="btn-icon btn-edit" title="Sửa" onclick="openEditUserModal('<?php echo htmlspecialchars($user['id']); ?>')" data-permission="user_edit"><i class="fas fa-pencil-alt"></i></button>
                                            <?php
                                                $is_inactive = isset($user['deleted_at']) && $user['deleted_at'] !== null && $user['deleted_at'] !== '';
                                                $action = $is_inactive ? 'enable' : 'disable';
                                                $icon = $is_inactive ? 'fa-toggle-on' : 'fa-toggle-off';
                                                $title = $is_inactive ? 'Kích hoạt' : 'Vô hiệu hóa';
                                                $btn_class = $is_inactive ? 'btn-success' : 'btn-secondary';
                                            ?>
                                            <button class="btn-icon <?php echo $btn_class; ?>" onclick="toggleUserStatus('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo $action; ?>')" title="<?php echo $title; ?>">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="10">Không tìm thấy người dùng phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-footer">
                 <div class="pagination-info">
                    <?php if ($total_items > 0):
                        $start_item = ($current_page - 1) * $items_per_page + 1;
                        $end_item = min($start_item + $items_per_page - 1, $total_items);
                    ?>
                        Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> người dùng
                    <?php else: ?>
                        Không có người dùng nào
                    <?php endif; ?>
                </div>
                <?php if ($total_pages > 1): ?>
                <div class="pagination-controls">
                    <button onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . $i; ?>'"><?php echo $i; ?></button>
                    <?php endfor; ?>
                    <button onclick="window.location.href='<?php echo $pagination_base_url . '&page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
                </div>
                 <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Chi tiết Người dùng</h4>
            <span class="modal-close" onclick="closeModal('viewUserModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewUserDetailsBody">
            <p>Đang tải...</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('viewUserModal')">Đóng</button>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
         <form id="editUserForm">
            <div class="modal-header">
                <h4>Chỉnh sửa Người dùng</h4>
                <span class="modal-close" onclick="closeModal('editUserModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label for="editUsername">Tên đăng nhập</label>
                    <input type="text" id="editUsername" name="username" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="editPhone">Số điện thoại</label>
                    <input type="tel" id="editPhone" name="phone">
                </div>
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="editIsCompany" name="is_company" onchange="toggleCompanyFields('edit')">
                        <label for="editIsCompany">Là tài khoản công ty?</label>
                    </div>
                </div>
                <div class="company-fields" id="editCompanyFields">
                    <div class="form-group">
                        <label for="editCompanyName">Tên công ty</label>
                        <input type="text" id="editCompanyName" name="company_name">
                    </div>
                    <div class="form-group">
                        <label for="editTaxCode">Mã số thuế</label>
                        <input type="text" id="editTaxCode" name="tax_code">
                    </div>
                </div>
                 <div id="editUserError" style="color: red; margin-top: 10px; font-size: var(--font-size-sm);"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div id="createUserModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('createUserModal')">&times;</span>
        <h2>Thêm người dùng mới</h2>
        <form id="createUserForm">
            <div class="form-group">
                <label for="createUsername">Tên người dùng:</label>
                <input type="text" id="createUsername" name="username" required>
            </div>
            <div class="form-group">
                <label for="createEmail">Email:</label>
                <input type="email" id="createEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="createPassword">Mật khẩu:</label>
                <input type="password" id="createPassword" name="password" required>
            </div>
            <div class="form-group">
                <label for="createPhone">Số điện thoại:</label>
                <input type="tel" id="createPhone" name="phone">
            </div>
             <div class="form-group form-check">
                <input type="checkbox" id="createIsCompany" name="is_company" onchange="toggleCompanyFields('create')">
                <label for="createIsCompany">Là công ty?</label>
            </div>
            <div id="createCompanyFields" class="company-fields">
                <div class="form-group">
                    <label for="createCompanyName">Tên công ty:</label>
                    <input type="text" id="createCompanyName" name="company_name">
                </div>
                <div class="form-group">
                    <label for="createTaxCode">Mã số thuế:</label>
                    <input type="text" id="createTaxCode" name="tax_code">
                </div>
            </div>
            <div class="form-actions">
                 <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                 <button type="button" class="btn btn-secondary" onclick="closeModal('createUserModal')">Hủy</button>
            </div>
            <p id="createUserError" class="error-message"></p>
        </form>
    </div>
</div>

<script>
    const viewModal = document.getElementById('viewUserModal');
    const editModal = document.getElementById('editUserModal');
    const createModal = document.getElementById('createUserModal');
    const viewDetailsBody = document.getElementById('viewUserDetailsBody');
    const editUserForm = document.getElementById('editUserForm');
    const createUserForm = document.getElementById('createUserForm');
    const editCompanyFields = document.getElementById('editCompanyFields');
    const createCompanyFields = document.getElementById('createCompanyFields');
    const editUserError = document.getElementById('editUserError');
    const createUserError = document.getElementById('createUserError');
    const basePath = '<?php echo $base_path; ?>';

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
        if (modalId === 'editUserModal') {
            editUserForm.reset();
            editCompanyFields.classList.remove('visible');
            editUserError.textContent = '';
        } else if (modalId === 'createUserModal') {
            createUserForm.reset();
            createCompanyFields.classList.remove('visible');
            createUserError.textContent = '';
        }
    }

    function toggleCompanyFields(formType) {
        const isCompanyCheckbox = document.getElementById(`${formType}IsCompany`);
        const companyFieldsDiv = document.getElementById(`${formType}CompanyFields`);
        if (isCompanyCheckbox.checked) {
            companyFieldsDiv.classList.add('visible');
            document.getElementById(`${formType}CompanyName`).required = true;
            document.getElementById(`${formType}TaxCode`).required = true;
        } else {
            companyFieldsDiv.classList.remove('visible');
            document.getElementById(`${formType}CompanyName`).required = false;
            document.getElementById(`${formType}TaxCode`).required = false;
        }
    }

    window.onclick = function(event) {
        if (event.target == viewModal) {
            closeModal('viewUserModal');
        }
        if (event.target == editModal) {
            closeModal('editUserModal');
        }
        if (event.target == createModal) {
             closeModal('createUserModal');
        }
    }

    function openCreateUserModal() {
        createUserForm.reset();
        createCompanyFields.classList.remove('visible');
        createUserError.textContent = '';
        createModal.style.display = 'block';
    }

    createUserForm.addEventListener('submit', function(event) {
        event.preventDefault();
        createUserError.textContent = '';
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang thêm...';

        const formData = new FormData(createUserForm);
        const data = Object.fromEntries(formData.entries());
        data.is_company = data.is_company ? 1 : 0;

        fetch(`${basePath}private/actions/setting/process_user_create.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
        })
        .then(response => {
            return response.text().then(text => {
                const ct = response.headers.get("content-type") || "";
                if (response.ok && ct.includes("application/json")) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Phản hồi JSON không hợp lệ từ máy chủ: ' + text);
                    }
                } else {
                    let errorMsg = `Lỗi HTTP ${response.status}: ${response.statusText}`;
                    if (text) {
                        errorMsg += ` - Phản hồi từ máy chủ: ${text.substring(0, 200)}${text.length > 200 ? '...' : ''}`;
                    }
                    throw new Error(errorMsg);
                }
            });
        })
        .then(result => {
            if (result.success) {
                closeModal('createUserModal');
                alert('Thêm người dùng thành công!');
                location.reload();
            } else {
                createUserError.textContent = 'Lỗi: ' + (result.message || 'Không thể thêm người dùng.');
            }
        })
        .catch(err => {
            console.error('Error creating user:', err);
            createUserError.textContent = 'Đã xảy ra lỗi: ' + err.message;
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Thêm người dùng';
        });
    });

    function viewUserDetails(userId) {
        viewDetailsBody.innerHTML = '<p>Đang tải...</p>';
        viewModal.style.display = 'block';

        fetch(`${basePath}private/actions/setting/fetch_user_details.php?user_id=${userId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success && result.data) {
                    const user = result.data;
                    let detailsHtml = `
                        <div class="detail-row"><span class="detail-label">ID:</span> <span class="detail-value">${user.id}</span></div>
                        <div class="detail-row"><span class="detail-label">Tên đăng nhập:</span> <span class="detail-value">${user.username || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value">${user.email}</span></div>
                        <div class="detail-row"><span class="detail-label">Số điện thoại:</span> <span class="detail-value">${user.phone || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Loại tài khoản:</span> <span class="detail-value">${user.account_type_text}</span></div>
                    `;
                    if (user.is_company) {
                        detailsHtml += `
                            <div class="detail-row"><span class="detail-label">Tên công ty:</span> <span class="detail-value">${user.company_name || '-'}</span></div>
                            <div class="detail-row"><span class="detail-label">Mã số thuế:</span> <span class="detail-value">${user.tax_code || '-'}</span></div>
                        `;
                    }
                    detailsHtml += `
                        <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${user.created_at_formatted}</span></div>
                        <div class="detail-row"><span class="detail-label">Cập nhật lần cuối:</span> <span class="detail-value">${user.updated_at_formatted}</span></div>
                        <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${user.status_text} ${user.deleted_at ? '(' + user.deleted_at_formatted + ')' : ''}</span></div>
                    `;
                    viewDetailsBody.innerHTML = detailsHtml;
                } else {
                    viewDetailsBody.innerHTML = `<p style="color: red;">Lỗi: ${result.message || 'Không thể tải thông tin người dùng.'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
                viewDetailsBody.innerHTML = `<p style="color: red;">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}</p>`;
            });
    }

    function openEditUserModal(userId) {
        editUserForm.reset();
        editCompanyFields.classList.remove('visible');
        editUserError.textContent = '';
        editModal.style.display = 'block';

        fetch(`${basePath}private/actions/setting/fetch_user_details.php?user_id=${userId}`)
            .then(response => response.ok ? response.json() : Promise.reject(`HTTP error! status: ${response.status}`))
            .then(result => {
                if (result.success && result.data) {
                    const user = result.data;
                    document.getElementById('editUserId').value = user.id;
                    document.getElementById('editUsername').value = user.username || '';
                    document.getElementById('editEmail').value = user.email || '';
                    document.getElementById('editPhone').value = user.phone || '';
                    document.getElementById('editIsCompany').checked = user.is_company == 1;
                    toggleCompanyFields('edit');
                    if (user.is_company) {
                        document.getElementById('editCompanyName').value = user.company_name || '';
                        document.getElementById('editTaxCode').value = user.tax_code || '';
                    }
                } else {
                    editUserError.textContent = `Lỗi tải dữ liệu: ${result.message || 'Không thể lấy thông tin người dùng.'}`;
                }
            })
            .catch(error => {
                console.error('Error fetching user details for edit:', error);
                editUserError.textContent = `Đã xảy ra lỗi khi tải dữ liệu: ${error}`;
            });
    }

    editUserForm.addEventListener('submit', function(event) {
        event.preventDefault();
        editUserError.textContent = '';
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang lưu...';

        fetch('<?php echo $base_path; ?>private/actions/setting/process_user_update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Cập nhật thành công!');
                closeModal('editUserModal');
                window.location.reload();
            } else {
                editUserError.textContent = 'Lỗi: ' + (data.message || 'Không thể cập nhật người dùng.');
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
            editUserError.textContent = 'Đã xảy ra lỗi khi gửi yêu cầu: ' + error.message;
        })
        .finally(() => {
             submitButton.disabled = false;
             submitButton.textContent = 'Lưu thay đổi';
        });
    });

    function toggleUserStatus(userId, action) {
        if (confirm(`Bạn có chắc muốn ${action === 'disable' ? 'vô hiệu hóa' : 'kích hoạt'} người dùng này không?`)) {
            fetch('<?php echo $base_path; ?>private/actions/setting/process_user_toggle_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: `user_id=${userId}&action=${action}`
            })
            .then(response => {
                const ct = response.headers.get("content-type") || "";
                return response.text().then(text => {
                    const firstChar = text.trim()[0];
                    if (response.ok && ct.includes("application/json") &&
                        (firstChar === "{" || firstChar === "[")) {
                    return JSON.parse(text);
                    }
                    let msg = `Lỗi HTTP ${response.status}: ${response.statusText}`;
                    msg += ` – server trả về:\n${text.substr(0,200)}`;
                    throw new Error(msg);
                });
            })
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Thao tác thành công!');
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể thay đổi trạng thái người dùng.'));
                }
            })
            .catch(error => {
                console.error('Error toggling user status:', error);
                alert('Đã xảy ra lỗi tạm thời: ' + error.message + '. Hãy load lại trang để xem kết quả.');
            });
        }
    }
</script>

</body>
</html>