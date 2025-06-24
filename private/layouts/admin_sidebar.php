<?php
// Define the base path relative to the location of the files including this sidebar
// Assuming the including file is in public/pages/, the path to public/ is ../
// Adjust this if the including file location is different.

// Include base constants
require_once __DIR__ . '/../config/constants.php';

// Admin navigation items - Updated structure with submenus for grouping
$admin_nav_items = [
    ['label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => '/public/pages/dashboard/dashboard.php', 'active_check' => 'dashboard.php', 'permission' => 'dashboard'],

    ['type' => 'section', 'label' => 'Quản lý Hệ thống'],
    ['label' => 'QL Trạm', 'icon' => 'fa-broadcast-tower', 'url' => '/public/pages/station/station_management.php', 'active_check' => 'station_management.php', 'permission' => 'station_management_view'],
    ['label' => 'QL Voucher', 'icon' => 'fa-ticket-alt', 'url' => '/public/pages/voucher/voucher_management.php', 'active_check' => 'voucher_management.php', 'permission' => 'voucher_management_view'],
    ['label' => 'QL phân quyền', 'icon' => 'fa-user-lock', 'url' => '/public/pages/auth/permission_management.php', 'active_check' => 'permission_management.php', 'permission' => 'permission_management_view'],

    ['type' => 'section', 'label' => 'Quản lý Khách hàng'],
    ['label' => 'Người dùng & Tài khoản', 'icon' => 'fa-users-cog', 'children' => [
        ['label' => 'QL người dùng', 'icon' => 'fa-users', 'url' => '/public/pages/user/user_management.php', 'active_check' => 'user_management.php', 'permission' => 'user_management_view'],
        ['label' => 'QL TK đo đạc', 'icon' => 'fa-ruler-combined', 'url' => '/public/pages/account/account_management.php', 'active_check' => 'account_management.php', 'permission' => 'account_management_view'],
    ]],
    ['label' => 'Khách hàng & Hỗ trợ', 'icon' => 'fa-hands-helping', 'children' => [
        ['label' => 'QL người giới thiệu', 'icon' => 'fa-network-wired', 'url' => '/public/pages/referral/referral_management.php', 'active_check' => 'referral_management.php', 'permission' => 'referral_management_view'],
        ['label' => 'Chăm sóc khách hàng', 'icon' => 'fa-headset', 'url' => '/public/pages/support/support_management.php', 'active_check' => 'support_management.php', 'permission' => 'support_management_view'],
        ['label' => 'QL Hướng dẫn', 'icon' => 'fa-book', 'url' => '/public/pages/guide/guide_management.php', 'active_check' => 'guide_management.php', 'permission' => 'guide_management_view'],
    ]],

    ['type' => 'section', 'label' => 'Quản lý Tài chính'],
    ['label' => 'Giao dịch & Doanh thu', 'icon' => 'fa-file-invoice-dollar', 'children' => [
        ['label' => 'QL giao dịch', 'icon' => 'fa-file-invoice-dollar', 'url' => '/public/pages/purchase/invoice_management.php', 'active_check' => 'invoice_management.php', 'permission' => 'invoice_management_view'],
        ['label' => 'QL doanh thu', 'icon' => 'fa-dollar-sign', 'url' => '/public/pages/purchase/revenue_management.php', 'active_check' => 'revenue_management.php', 'permission' => 'revenue_management_view'],
        ['label' => 'Phê duyệt hóa đơn', 'icon' => 'fa-file-invoice', 'url' => '/public/pages/invoice/invoice_review.php', 'active_check' => 'invoice_review.php', 'permission' => 'invoice_review_view'],
    ]],

    ['label' => 'Báo cáo', 'icon' => 'fa-chart-line', 'url' => '/public/pages/report/reports.php', 'active_check' => 'reports.php', 'permission' => 'reports_view'],

    ['type' => 'section', 'label' => 'Cài đặt'],
    ['label' => 'Thông tin tài khoản', 'icon' => 'fa-id-card', 'url' => '/public/pages/setting/profile.php', 'active_check' => 'profile.php', 'permission' => 'settings'],
    ['label' => 'Đăng xuất', 'icon' => 'fa-sign-out-alt', 'url' => '/public/pages/auth/admin_logout.php', 'class' => 'logout-link'],
];

// Load DB and fetch allowed permissions for current role
require_once BASE_PATH . '/classes/Database.php';
// Replace direct constructor call with singleton
$db  = Database::getInstance();
$pdo = $db->getConnection();

$role = isset($_SESSION['admin_role']) ? strtolower($_SESSION['admin_role']) : '';
$allowed_perms = [];
$admin_db_name = 'Admin'; // Default name
$db_custom_role_names = []; // Will hold names from custom_roles table

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

    // Fetch all custom role display names from DB
    $stmt_custom_roles = $pdo->query("SELECT role_key, role_display_name FROM custom_roles");
    if ($stmt_custom_roles) {
        while ($row = $stmt_custom_roles->fetch(PDO::FETCH_ASSOC)) {
            $db_custom_role_names[$row['role_key']] = $row['role_display_name'];
        }
    }
}

// Use the name fetched from DB
$admin_user_name = $admin_db_name;

// Determine display role name: custom_roles from DB first, then default
if (isset($db_custom_role_names[$role])) {
    $admin_user_role = htmlspecialchars($db_custom_role_names[$role]);
} else {
    $admin_user_role = ucfirst(htmlspecialchars(str_replace('_', ' ', $role)));
}
?>
<!-- Hamburger button (Positioned fixed via CSS) -->
<button id="hamburger-btn" class="hamburger-btn">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar" id="admin_sidebar"> <!-- Use 'sidebar' class -->
    <!-- Logo & Toggle -->
    <div class="sidebar-header">
         <!-- Link to admin dashboard or # -->
        <a href="<?php echo $base_url; ?>public/pages/dashboard/dashboard.php" class="logo-link"> <!-- Updated link -->
            <i class="logo-icon fas fa-user-shield"></i>
            <span class="logo-text">Trang Quản Trị</span>
        </a>
        <button id="collapse-btn" class="collapse-btn" title="Thu gọn thanh điều hướng">
            <i class="fas fa-angle-double-left"></i>
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
                    <?php if (isset($item['type']) && $item['type'] === 'section'): ?>
                        <li class="nav-section-title-li">
                            <p class="nav-section-title"><?php echo htmlspecialchars($item['label']); ?></p>
                        </li>
                    <?php elseif (isset($item['children'])): ?>
                        <?php
                            // Parent menu with submenu
                            $any_allowed = false;
                            foreach ($item['children'] as $sub) {
                                if (in_array($sub['permission'], $allowed_perms)) { $any_allowed = true; break; }
                            }
                            if (!$any_allowed) continue;
                        ?>
                        <li class="nav-parent">
                            <a href="javascript:void(0)" class="nav-item parent-toggle" data-menu-key="<?php echo htmlspecialchars($item['label']); ?>">
                                <i class="icon fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                                <span><?php echo htmlspecialchars($item['label']); ?></span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </a>
                            <ul class="nav-submenu">
                                <?php foreach ($item['children'] as $sub): ?>
                                    <?php if (!in_array($sub['permission'], $allowed_perms)) continue; ?>
                                    <?php $active = $current_script === $sub['active_check'] ? ' active' : ''; ?>
                                    <li>
                                        <a href="<?php echo $base_url . ltrim($sub['url'], '/'); ?>" class="nav-item<?php echo $active; ?>">
                                            <i class="icon fas <?php echo htmlspecialchars($sub['icon']); ?>"></i>
                                            <span><?php echo htmlspecialchars($sub['label']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php
                            if (!isset($item['type'])
                                && isset($item['permission'])
                                && !in_array($item['permission'], $allowed_perms)
                                && !in_array($item['permission'], ['dashboard', 'settings'])
                            ) {
                                continue;
                            }
                            // Determine if the item is active
                            $is_active = false;
                            if (isset($item['active_check']) && $current_script === $item['active_check']) {
                                $is_active = true;
                            }

                            $item_class = isset($item['class']) ? $item['class'] : '';
                            if ($is_active) {
                                $item_class .= ' active';
                            }
                        ?>
                        <li>
                            <a href="<?php echo $base_url . ltrim($item['url'], '/'); ?>"
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
