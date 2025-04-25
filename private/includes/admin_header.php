<?php
// Ensure $base_path is defined as a string (fallback to '/rtk_web_admin/')
if (!isset($base_path) || !is_string($base_path)) {
    // Attempt to determine base path dynamically if possible, otherwise default
    // This might need adjustment based on your server setup / framework
    $script_name = $_SERVER['SCRIPT_NAME']; // e.g., /rtk_web_admin/public/pages/dashboard.php
    $base_path_parts = explode('/', $script_name);
    // Assuming structure is /project_root/public/pages/file.php
    // Adjust the slice index based on your actual structure
    if (count($base_path_parts) >= 3) {
         $base_path = '/' . $base_path_parts[1] . '/'; // Assumes project root is the first segment
    } else {
        $base_path = '/rtk_web_admin/'; // Fallback default
    }
}

// Ensure $base_path ends with a slash
if (substr($base_path, -1) !== '/') {
    $base_path .= '/';
}
$public_assets_path = $base_path . 'public/assets/';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title should be dynamic, passed from the including page -->
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Dashboard'; ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Updated Font Awesome -->

    <!-- Base styles, variables etc. -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css">

    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/main-content.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/sidebar.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/header.css"> <!-- Added header.css link -->

    <!-- Component styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/cards.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/forms.css"> <!-- Added forms.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/modals.css"> <!-- Added modals.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/badges.css"> <!-- Added badges.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/toasts.css"> <!-- Added toasts.css -->

    <!-- Page specific styles (e.g., dashboard) - Consider moving to separate files -->
    <style>
        /* Root variables (Keep or move to base.css) */
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

        /* Global Font & Body (Keep or move to base.css) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-800);
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* Content Header (Common element - Keep or move to components/header.css or main-content.css) */
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem 1.5rem; background: white; border-radius: var(--rounded-lg); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-header h2 { font-size: 1.5rem; font-weight: var(--font-semibold); color: var(--gray-800); }
        .user-info { display: flex; align-items: center; gap: 1rem; font-size: var(--font-size-sm); }
        .user-info span .highlight { color: var(--primary-600); font-weight: var(--font-semibold); }
        .user-info a { color: var(--primary-600); text-decoration: none; }
        .user-info a:hover { text-decoration: underline; }

        /* Content Section (Common element - Keep or move to components/sections.css or main-content.css) */
        .content-section { background: white; border-radius: var(--rounded-lg); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .content-section h3 { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--gray-700); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; }

        /* Filter Bar (Common element - Keep or move to components/filters.css) */
        .filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center; }
        .filter-bar input, .filter-bar select { padding: 0.6rem 0.8rem; border: 1px solid var(--gray-300); border-radius: var(--rounded-md); font-size: var(--font-size-sm); }
        .filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .filter-bar button, .filter-bar a.btn-secondary { padding: 0.6rem 1rem; font-size: var(--font-size-sm); }

        /* Header Actions (Common element - Keep or move to components/actions.css) */
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
        .header-actions h3 { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
        .header-actions .btn-primary { font-size: var(--font-size-sm); }

        /* Pagination Footer (Common element - Keep or move to components/pagination.css) */
        .pagination-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: var(--font-size-sm); color: var(--gray-600); }
        .pagination-controls { display: flex; gap: 0.3rem; }
        .pagination-controls button, .pagination-controls span { padding: 0.4rem 0.8rem; border: 1px solid var(--gray-300); background-color: #fff; border-radius: var(--rounded-md); font-size: var(--font-size-sm); display: inline-flex; align-items: center; justify-content: center; min-width: 32px; }
        .pagination-controls button { cursor: pointer; }
        .pagination-controls button:disabled { background-color: var(--gray-100); color: var(--gray-400); cursor: not-allowed; }
        .pagination-controls button.active { background-color: var(--primary-500); color: #fff; border-color: var(--primary-500); font-weight: bold; }
        .pagination-controls span { background-color: transparent; border: none; color: var(--gray-500); }

        /* No Results Row (Common for tables - Keep or move to components/tables.css) */
        #no-results-row td { text-align: center; padding: 3rem; color: var(--gray-500); font-size: var(--font-size-base); }

        /* Keep only mobile adjustments for specific components if needed */
        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 0.8rem 1rem;
            }
            .content-header h2 { font-size: 1.25rem; }
            .user-info {
                margin-top: 0.5rem;
                width: 100%;
                justify-content: space-between;
            }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .header-actions { flex-direction: column; align-items: flex-start; }
            .pagination-footer { flex-direction: column; gap: 0.75rem; align-items: center; }
        }
    </style>

    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('admin_sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;

            if (window.innerWidth < 1025) {
                // Mobile/Tablet: Toggle sidebar visibility and overlay
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
                body.classList.toggle('sidebar-is-open'); // For potential body scroll lock
            } else {
                // Desktop: Toggle collapsed state class on body
                body.classList.toggle('sidebar-is-collapsed');
                // Save collapsed state to localStorage
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-is-collapsed'));
            }
        }

        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('admin_sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;

            if (window.innerWidth >= 1025) {
                // Desktop: Apply collapsed state from localStorage
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    body.classList.add('sidebar-is-collapsed');
                } else {
                    body.classList.remove('sidebar-is-collapsed');
                }
                // Ensure mobile classes are removed
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                body.classList.remove('sidebar-is-open');
            } else {
                // Mobile: Default to closed state
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                body.classList.remove('sidebar-is-open');
                // Ensure desktop class is removed
                body.classList.remove('sidebar-is-collapsed');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('admin_sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;
            const isDesktop = window.innerWidth >= 1025;

            if (isDesktop) {
                // Transitioning to Desktop or resizing on Desktop
                // Ensure mobile classes are removed
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                body.classList.remove('sidebar-is-open');
                // Re-apply collapsed state from localStorage if it exists
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    if (!body.classList.contains('sidebar-is-collapsed')) {
                         body.classList.add('sidebar-is-collapsed');
                    }
                } else {
                     if (body.classList.contains('sidebar-is-collapsed')) {
                         body.classList.remove('sidebar-is-collapsed');
                     }
                }
            } else {
                // Transitioning to Mobile or resizing on Mobile
                // Ensure desktop class is removed
                body.classList.remove('sidebar-is-collapsed');
                // Force close sidebar on transition to mobile for simplicity
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                body.classList.remove('sidebar-is-open');
            }
        });
    </script>
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Hamburger & sidebar sẽ được include bên ngoài -->