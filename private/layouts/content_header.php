<?php
// Partial: reusable content header with user info and quick actions

// User info and role display logic (similar to sidebar)
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';

$admin_user_role_display = 'Admin'; // Default fallback
$user_display_name = 'Admin'; // Default fallback
$user_short_name = 'Admin'; // Default fallback for compact display
$role = $_SESSION['admin_role'] ?? '';

if (!empty($role)) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        if ($pdo && isset($_SESSION['admin_username'])) {
            // Fetch user's name from admin table
            $stmt_name = $pdo->prepare('SELECT name FROM admin WHERE admin_username = ?');
            $stmt_name->execute([$_SESSION['admin_username']]);
            $admin_name_result = $stmt_name->fetch(PDO::FETCH_ASSOC);
            if ($admin_name_result && isset($admin_name_result['name'])) {
                $user_display_name = htmlspecialchars($admin_name_result['name']);
                // Create short name (last name only) for compact display
                $name_parts = explode(' ', trim($admin_name_result['name']));
                $user_short_name = htmlspecialchars(end($name_parts));
            }
            
            // Fetch custom role display names from DB
            $stmt_custom_roles = $pdo->query("SELECT role_key, role_display_name FROM custom_roles");
            $db_custom_role_names = [];
            if ($stmt_custom_roles) {
                while ($row = $stmt_custom_roles->fetch(PDO::FETCH_ASSOC)) {
                    $db_custom_role_names[$row['role_key']] = $row['role_display_name'];
                }
            }
            
            // Determine display role name: custom_roles from DB first, then default
            if (isset($db_custom_role_names[$role])) {
                $admin_user_role_display = htmlspecialchars($db_custom_role_names[$role]);
            } else {
                $admin_user_role_display = ucfirst(htmlspecialchars(str_replace('_', ' ', $role)));
            }
        }
    } catch (Exception $e) {
        // If anything fails, keep the default
        error_log('Content header user info error: ' . $e->getMessage());
    }
}

// Determine show_quick_search based on user permissions
$app_permissions = require __DIR__ . '/../config/app_permissions.php';
$show_quick_search = false;
foreach (array_keys($app_permissions) as $perm) {
    if (substr($perm, -5) === '_view' && Auth::can($perm)) {
        $show_quick_search = true;
        break;
    }
}
?>
<div class="content-header">
    <div class="header-left">
        <div class="page-title-section">
            <h2><?= htmlspecialchars($page_title) ?></h2>
            <p class="page-subtitle">
                <span class="time-section">
                    <i class="fas fa-clock"></i>
                    <span id="current-time"></span>
                </span>
                
                <span class="system-status online-indicator" id="system-status" title="Hệ thống đang hoạt động">
                    <i class="fas fa-circle"></i> Online
                </span>
            </p>
        </div>
    </div>
    
    <div class="header-center">
        <?php if (
            isset($show_quick_search) && $show_quick_search
        ): ?>
        <!-- Quick Search -->
        <div class="quick-search">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" 
                       id="global-search" 
                       placeholder="Tìm kiếm người dùng, hóa đơn, trạm... (Ctrl+K)"
                       autocomplete="off"
                       title="Nhấn Ctrl+K để focus vào ô tìm kiếm">
                <div class="search-results" id="search-results"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="header-right">
        <!-- Notifications -->
        <div class="notification-bell" id="notification-bell" title="Thông báo (Ctrl+Shift+H)">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" id="notification-count">3</span>
            <div class="notification-dropdown" id="notification-dropdown">
                <div class="notification-header">
                    <h4>Thông báo</h4>
                    <button class="mark-all-read">Đánh dấu đã đọc</button>
                </div>
                <div class="notification-list" id="notification-list">
                    <!-- Notifications will be loaded here -->
                </div>
                <div class="notification-footer">
                    <a href="<?= $base_url ?>public/pages/dashboard/dashboard.php">Xem tất cả</a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="quick-action-btn" id="quick-actions-toggle" title="Thao tác nhanh (Ctrl+Shift+F)">
                <i class="fas fa-plus"></i>
            </button>
            <div class="quick-actions-menu" id="quick-actions-menu">
                <a href="<?= $base_url ?>public/pages/purchase/invoice_management.php" class="quick-action-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>QL giao dịch</span>
                </a>
                <a href="<?= $base_url ?>public/pages/invoice/invoice_review.php" class="quick-action-item">
                    <i class="fas fa-file-invoice"></i>
                    <span>Phê duyệt hóa đơn</span>
                </a>
                <a href="<?= $base_url ?>public/pages/support/support_management.php" class="quick-action-item">
                    <i class="fas fa-headset"></i>
                    <span>Chăm sóc khách hàng</span>
                </a>
                <a href="<?= $base_url ?>public/pages/station/station_management.php" class="quick-action-item">
                    <i class="fas fa-broadcast-tower"></i>
                    <span>Quản lý trạm</span>
                </a>
                <a href="<?= $base_url ?>public/pages/report/reports.php" class="quick-action-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Xem báo cáo</span>
                </a>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="user-menu">
            <button class="user-menu-trigger" id="user-menu-trigger">
                <div class="user-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="user-name user-name-full"><?= htmlspecialchars($user_display_name) ?></span>
                <span class="user-name user-name-short"><?= htmlspecialchars($user_short_name) ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-menu-dropdown" id="user-menu-dropdown">
                <div class="user-info-card">
                    <div class="user-avatar-large">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="user-details">
                        <h4><?= htmlspecialchars($user_display_name) ?></h4>
                        <p><?= $admin_user_role_display ?></p>
                    </div>
                </div>
                <div class="menu-divider"></div>
                <a href="<?= $base_url ?>public/pages/setting/profile.php" class="menu-item">
                    <i class="fas fa-user-cog"></i>
                    <span>Hồ sơ cá nhân</span>
                </a>
                <a href="<?= $base_url ?>public/pages/dashboard/dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Bảng điều khiển</span>
                </a>
                <div class="menu-divider"></div>
                <a href="<?= $base_url ?>public/pages/auth/admin_logout.php" class="menu-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </div>
    </div>
</div>
