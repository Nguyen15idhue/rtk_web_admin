<?php
// Define the base path relative to the location of the files including this sidebar
// Assuming the including file is in public/pages/, the path to public/ is ../
// Adjust this if the including file location is different.
$base_path_for_assets = '../assets'; // Relative path to assets folder

// Admin navigation items - Updated structure
$admin_nav_items = [
    // Main Navigation
    ['label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => '/public/pages/dashboard.php', 'active_check' => 'dashboard.php'],
    ['label' => 'QL người dùng', 'icon' => 'fa-users', 'url' => '/public/pages/user_management.php', 'active_check' => 'user_management.php'], // Assuming URL based on original onclick target
    ['label' => 'QL TK đo đạc', 'icon' => 'fa-ruler-combined', 'url' => '/public/pages/account_management.php', 'active_check' => 'account_management.php'], // Assuming URL based on original onclick target
    ['label' => 'QL hóa đơn/GD', 'icon' => 'fa-file-invoice-dollar', 'url' => '/public/pages/invoice_management.php', 'active_check' => 'invoice_management.php'], // Assuming URL based on original onclick target
    ['label' => 'QL người giới thiệu', 'icon' => 'fa-network-wired', 'url' => '/public/pages/referral_management.php', 'active_check' => 'referral_management.php'], // Assuming URL based on original onclick target
    ['label' => 'Báo cáo', 'icon' => 'fa-chart-line', 'url' => '/public/pages/reports.php', 'active_check' => 'reports.php'], // Assuming URL based on original onclick target
    ['label' => 'QL phân quyền', 'icon' => 'fa-user-lock', 'url' => '/public/pages/permission_management.php', 'active_check' => 'permission_management.php'], // Assuming URL based on original onclick target

    // Cài đặt section
    ['type' => 'section', 'label' => 'Cài đặt'],
    ['label' => 'Thông tin tài khoản', 'icon' => 'fa-id-card', 'url' => '/public/pages/profile.php', 'active_check' => 'profile.php'], // Assuming URL based on original onclick target

    // Logout
    // Keeping logout separate as per original structure, but using URL
    ['label' => 'Đăng xuất', 'icon' => 'fa-sign-out-alt', 'url' => '/public/pages/auth/admin_logout.php', 'class' => 'logout-link'] // Using URL instead of onclick
];

// Placeholder for admin user info (replace with actual session/database data if available)
$admin_user_name = 'Admin Name'; // Example: isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin';
$admin_user_role = 'Super Admin'; // Example: isset($_SESSION['admin_role']) ? htmlspecialchars($_SESSION['admin_role']) : 'Administrator';

// Include sidebar CSS - Adjust path as needed
echo '<link rel="stylesheet" href="' . $base_path_for_assets . '/css/layouts/sidebar.css">';
?>
<!-- Hamburger button (outside sidebar for positioning) -->
<button id="hamburger-btn" class="hamburger-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar" id="admin_sidebar"> <!-- Use 'sidebar' class from example -->
    <!-- Logo & Toggle -->
    <div class="sidebar-header">
         <!-- Link to admin dashboard or # -->
        <a href="#" class="logo-link" onclick="showSectionAndToggleSidebar('admin-dashboard', this); return false;">
            <i class="logo-icon fas fa-user-shield"></i>
            <span class="logo-text">Trang Quản Trị</span>
        </a>
        <button class="close-button" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

     <!-- Admin User Info -->
    <div class="user-info-container">
        <div class="user-info">
            <div class="user-icon-wrapper">
                <i class="fas fa-user-tie"></i> <!-- Admin icon -->
            </div>
            <div class="user-text">
                 <!-- Use dynamic variables -->
                <p id="admin-user-name" class="user-name"><?php echo $admin_user_name; ?></p>
                <p id="admin-user-role" class="user-role"><?php echo $admin_user_role; ?></p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
     <!-- Use sidebar-nav-container for consistency if needed for scrolling -->
    <div class="sidebar-nav-container">
        <nav class="sidebar-nav" id="admin-main-menu"> <!-- Use 'sidebar-nav' class -->
            <ul>
                <?php foreach ($admin_nav_items as $item): ?>
                    <?php if (isset($item['type']) && $item['type'] === 'section'): ?>
                        <li class="nav-section-title-li"> <!-- Class from example -->
                            <p class="nav-section-title"><?php echo htmlspecialchars($item['label']); ?></p>
                        </li>
                    <?php else: ?>
                        <li>
                             <!-- Use 'a' tag with href based on 'url'. Removed onclick and data-permission -->
                            <a href="<?php echo htmlspecialchars($item['url']); ?>"
                               class="nav-item <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                                <i class="icon fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                                <span><?php echo htmlspecialchars($item['label']); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>

<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>
<!-- =================== HẾT SIDEBAR (ADMIN) =================== -->
