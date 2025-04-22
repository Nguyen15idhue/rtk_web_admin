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

        .stat-card .icon {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border-radius: 50%;
        }
    </style>
</head>
<body>
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

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200" style="background-color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4" style="font-size: 1.1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Đăng ký mới (7 ngày)</h3>
                <div class="chart-container" style="min-height: 200px; position: relative;">
                    <canvas id="newRegistrationsChart"></canvas>
                </div>
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

<!-- Add Chart.js library if not already included globally -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- New Registrations Chart ---
    const newRegCtx = document.getElementById('newRegistrationsChart');
    if (newRegCtx) {
        // --- IMPORTANT ---
        // You need to fetch this data in PHP (e.g., in fetch_dashboard_data.php)
        // and make it available here. Example structure:
        <?php
            // $new_registrations_chart_data should be fetched in fetch_dashboard_data.php
            $new_registrations_chart_data = $data['new_registrations_chart_data'];
        ?>
        const newRegistrationsData = <?php echo json_encode($new_registrations_chart_data); ?>;

        new Chart(newRegCtx, {
            type: 'line', // Or 'bar'
            data: {
                labels: newRegistrationsData.labels,
                datasets: [{
                    label: 'Đăng ký mới',
                    data: newRegistrationsData.data,
                    borderColor: 'rgb(59, 130, 246)', // blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // Ensure whole numbers for counts
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide legend if only one dataset
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    }

    // --- Placeholder for Referral Chart ---
    // Add similar logic here for the "Giới thiệu HĐ (7 ngày)" chart
    // const referralCtx = document.getElementById('referralChart'); // Add an ID to its canvas
    // if (referralCtx) { ... }
});
</script>

<!-- Note: Sidebar and Footer includes should be handled by the main layout file -->
<!-- Removed JS from here, it should be in a global JS file or footer include -->