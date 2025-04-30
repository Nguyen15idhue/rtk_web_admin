<?php
// Define the base path relative to the location of the files including this sidebar
// Assuming the including file is in public/pages/, the path to public/ is ../
// Adjust this if the including file location is different.

// Admin navigation items - Updated structure
$admin_nav_items = [
    ['label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => '/public/pages/dashboard/dashboard.php', 'active_check' => 'dashboard.php', 'permission' => 'dashboard'],
    ['label' => 'QL người dùng', 'icon' => 'fa-users', 'url' => '/public/pages/user/user_management.php', 'active_check' => 'user_management.php', 'permission' => 'user_management'],
    ['label' => 'QL TK đo đạc', 'icon' => 'fa-ruler-combined', 'url' => '/public/pages/account_management.php', 'active_check' => 'account_management.php', 'permission' => 'account_management'],
    ['label' => 'QL giao dịch', 'icon' => 'fa-file-invoice-dollar', 'url' => '/public/pages/invoice/invoice_management.php', 'active_check' => 'invoice_management.php', 'permission' => 'invoice_management'],
    ['label' => 'QL doanh thu', 'icon' => 'fa-dollar-sign', 'url' => '/public/pages/invoice/revenue_management.php', 'active_check' => 'revenue_management.php', 'permission' => 'revenue_management'],
    ['label' => 'QL người giới thiệu', 'icon' => 'fa-network-wired', 'url' => '/public/pages/referral_management.php', 'active_check' => 'referral_management.php', 'permission' => 'referral_management'],
    ['label' => 'Báo cáo', 'icon' => 'fa-chart-line', 'url' => '/public/pages/reports.php', 'active_check' => 'reports.php', 'permission' => 'reports'],
    ['label' => 'QL hướng dẫn', 'icon' => 'fa-book', 'url' => '/public/pages/guide/guide_management.php', 'active_check' => 'guide_management.php', 'permission' => 'guide_management'],
    ['label' => 'Phê duyệt hóa đơn', 'icon' => 'fa-file-invoice', 'url' => '/public/pages/invoice_requests/invoice_review.php', 'active_check' => 'invoice_review.php', 'permission' => 'invoice_review'],
    ['label' => 'QL phân quyền', 'icon' => 'fa-user-lock', 'url' => '/public/pages/auth/permission_management.php', 'active_check' => 'permission_management.php', 'permission' => 'permission_management'],
    ['type' => 'section', 'label' => 'Cài đặt'],
    ['label' => 'Thông tin tài khoản', 'icon' => 'fa-id-card', 'url' => '/public/pages/setting/profile.php', 'active_check' => 'profile.php', 'permission' => 'settings'],
    ['label' => 'Đăng xuất', 'icon' => 'fa-sign-out-alt', 'url' => '/public/pages/auth/admin_logout.php', 'class' => 'logout-link']
];

// Load DB and fetch allowed permissions for current role
require_once __DIR__ . '/../classes/Database.php';
// Replace direct constructor call with singleton
$db  = Database::getInstance();
$pdo = $db->getConnection();

$role = isset($_SESSION['admin_role']) ? strtolower($_SESSION['admin_role']) : '';
$allowed_perms = [];
$admin_db_name = 'Admin'; // Default name

if ($pdo && $role && isset($_SESSION['admin_username'])) {
    // Fetch allowed permissions
    $stmt_perms = $pdo->prepare('SELECT permission FROM role_permissions WHERE role=? AND allowed=1');
    $stmt_perms->execute([$role]);
    $allowed_perms = $stmt_perms->fetchAll(PDO::FETCH_COLUMN);

    // Fetch user's name from admin table
    $stmt_name = $pdo->prepare('SELECT name FROM admin WHERE admin_username = ?');
    $stmt_name->execute([$_SESSION['admin_username']]);
    $admin_name_result = $stmt_name->fetch(PDO::FETCH_ASSOC);
    if ($admin_name_result && isset($admin_name_result['name'])) {
        $admin_db_name = htmlspecialchars($admin_name_result['name']);
    }
}

// Vietnamese role mapping
$role_vietnamese = [
    'admin' => 'Quản trị viên',
    'customercare' => 'Chăm sóc khách hàng',
    // Add other roles here if needed
];

// Use the name fetched from DB
$admin_user_name = $admin_db_name;
// Use Vietnamese role name if available, otherwise fallback to capitalized English role
$admin_user_role = isset($role_vietnamese[$role]) ? $role_vietnamese[$role] : ucfirst(htmlspecialchars($role));
?>
<!-- Hamburger button (Positioned fixed via CSS) -->
<button id="hamburger-btn" class="hamburger-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar" id="admin_sidebar"> <!-- Use 'sidebar' class -->
    <!-- Logo & Toggle -->
    <div class="sidebar-header">
         <!-- Link to admin dashboard or # -->
        <a href="<?php echo $base_path; ?>public/pages/dashboard/dashboard.php" class="logo-link"> <!-- Updated link -->
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
    <div class="sidebar-nav-container">
        <nav class="sidebar-nav" id="admin-main-menu"> <!-- Use 'sidebar-nav' class -->
            <ul>
                <?php
                // Get current script name for active state check
                $current_script = basename($_SERVER['PHP_SELF']);
                ?>
                <?php foreach ($admin_nav_items as $item): ?>
                    <?php
                        // Skip unauthorized items (except sections or logout)
                        if (!isset($item['type']) && isset($item['permission']) && !in_array($item['permission'], $allowed_perms)) {
                            continue;
                        }
                    ?>
                    <?php if (isset($item['type']) && $item['type'] === 'section'): ?>
                        <li class="nav-section-title-li"> <!-- Class from example -->
                            <p class="nav-section-title"><?php echo htmlspecialchars($item['label']); ?></p>
                        </li>
                    <?php else:
                        // Determine if the item is active
                        $is_active = false;
                        if (isset($item['active_check']) && $current_script === $item['active_check']) {
                            $is_active = true;
                        }
                        // Handle dashboard specifically if base path matches URL
                        elseif (isset($item['active_check']) && $item['active_check'] === 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], $item['url']) !== false) {
                             // Basic check, might need refinement depending on URL structure
                             // $is_active = true; // Uncomment if dashboard needs special check
                        }

                        $item_class = isset($item['class']) ? $item['class'] : '';
                        if ($is_active) {
                            $item_class .= ' active';
                        }
                    ?>
                        <li>
                             <!-- Use 'a' tag with href. Add active class dynamically -->
                            <a href="<?php echo $base_path . ltrim($item['url'], '/'); ?>"
                               class="nav-item <?php echo trim($item_class); ?>">
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
