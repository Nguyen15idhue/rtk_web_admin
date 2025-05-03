<?php
$bootstrap_data = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_includes_path = $bootstrap_data['private_includes_path'];

// Redirect nếu chưa đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$page_title = 'Quản lý hướng dẫn';

include $private_includes_path . 'admin_header.php';
include $private_includes_path . 'admin_sidebar.php'; 
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo $user_display_name; // Already HTML-escaped in bootstrap ?></span>!</span>
                <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
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

<script>
    window.basePath = '<?php echo rtrim($base_url,'/'); ?>';
</script>
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo $base_url; ?>public/assets/js/pages/guide/guide_management.js"></script>

<?php include $private_includes_path . 'admin_footer.php'; ?>