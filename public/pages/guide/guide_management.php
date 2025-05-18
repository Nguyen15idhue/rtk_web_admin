<?php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];

// Redirect nếu chưa đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

// Check permissions
$canEditGuide = Auth::can('guide_management_edit');

$page_title = 'Quản lý hướng dẫn';

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php'; 
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách Guide</h3>
            <?php if ($canEditGuide): ?>
            <button class="btn btn-primary" onclick="window.location.href='edit_guide.php'">
                <i class="fas fa-plus"></i> Thêm mới
            </button>
            <?php endif; ?>
        </div>
        <form method="GET" class="filter-bar">
            <input type="search" name="search" placeholder="Tìm tiêu đề/topic" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'],'?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>

        <div class="table-wrapper">
            <table class="table" id="tbl-guides">
                <thead>
                    <tr>
                        <th>ID</th><th>Tiêu đề</th><th>Tác giả</th><th class="status text-center">Trạng thái</th><th class="actions text-center">Hành động</th>
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
    window.appConfig = {
        permissions: {
            guide_management_edit: <?php echo json_encode($canEditGuide); ?>
        }
    };
</script>
<script defer src="<?php echo $base_url; ?>public/assets/js/pages/guide/guide_management.js"></script>

<?php include $private_layouts_path . 'admin_footer.php'; ?>