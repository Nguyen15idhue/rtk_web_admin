<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/admin_login.php');
    exit;
}

// Include functions.php first to use standardized path functions
require_once __DIR__ . '/../../../private/utils/functions.php';

// Use the standardized base path function instead of manual calculation
$base_path = get_base_path();
$private_includes = get_private_path() . 'includes/';

$page_title = 'Quản lý hướng dẫn';
$bootstrap_data = require_once $private_includes . 'page_bootstrap.php';
$user_display_name = $bootstrap_data['user_display_name'];

include $private_includes . 'admin_header.php';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/badges.css">
<?php include $private_includes . 'admin_sidebar.php'; ?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo $user_display_name; // Already HTML-escaped in bootstrap ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
    </div>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Guide</h3>
            <button class="btn btn-primary" onclick="window.location.href='edit_guide.php'">
                <i class="fas fa-plus"></i> Thêm mới
            </button>
        </div>
        <form method="GET" class="filter-bar">
            <input type="search" name="search" placeholder="Tìm tiêu đề/topic" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>

        <div class="transactions-table-wrapper">
            <table class="transactions-table" id="tbl-guides">
                <thead>
                    <tr>
                        <th>ID</th><th>Tiêu đề</th><th>Tác giả</th><th class="status" style="text-align:center">Trạng thái</th><th class="actions" style="text-align:center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- rows populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // expose basePath for external script
    window.basePath = '<?php echo rtrim($base_path,'/'); ?>';
</script>
<script src="<?php echo $base_path; ?>public/assets/js/pages/guide/guide_management.js"></script>

<?php include $private_includes . 'admin_footer.php'; ?>