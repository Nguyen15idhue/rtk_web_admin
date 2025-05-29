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

// Server-side pagination setup
$search = $_GET['search'] ?? '';
$topic = $_GET['topic'] ?? '';
$status = $_GET['status'] ?? '';
$current_page = max(1, intval($_GET['page'] ?? 1));
$items_per_page = DEFAULT_ITEMS_PER_PAGE;
$model = new GuideModel();

// Optimized pagination - count and fetch separately
$total_items = $model->getCount($search, $topic, $status);
$total_pages = (int)ceil($total_items / $items_per_page);
$offset = ($current_page - 1) * $items_per_page;
$guides = $model->getPaginated($search, $topic, $status, $items_per_page, $offset);
$topics = $model->getDistinctTopics();
$pagination_base_url = strtok($_SERVER['REQUEST_URI'], '?');
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
        </div>        <form method="GET" class="filter-bar">
            <input type="search" name="search" placeholder="Tìm tiêu đề" value="<?php echo htmlspecialchars($search); ?>">
            <select name="topic" id="filter-topic">
                <option value="">Tất cả chủ đề</option>
                <?php foreach ($topics as $t): ?>
                    <option value="<?php echo htmlspecialchars($t); ?>" <?php if ($t === $topic) echo 'selected'; ?>><?php echo htmlspecialchars($t); ?></option>
                <?php endforeach; ?>
            </select>            <select name="status" id="filter-status">
                <option value="">Tất cả trạng thái</option>
                <option value="published" <?php if ($status === 'published') echo 'selected'; ?>>Đã xuất bản</option>
                <option value="draft" <?php if ($status === 'draft') echo 'selected'; ?>>Bản nháp</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa</a>
        </form>
        <div class="table-wrapper">
            <table class="table" id="tbl-guides">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Chủ đề</th>
                        <th>Tác giả</th>
                        <th class="status text-center">Trạng thái</th>
                        <th class="actions text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($guides)): ?>
                        <?php foreach ($guides as $g): ?>
                            <tr>
                                <td><?php echo $g['id']; ?></td>
                                <td><?php echo htmlspecialchars($g['title']); ?></td>
                                <td><?php echo $g['topic'] ? htmlspecialchars($g['topic']) : '<em>Chưa rõ</em>'; ?></td>
                                <td><?php echo $g['author_name'] ? htmlspecialchars($g['author_name']) : '<em>Chưa rõ</em>'; ?></td>
                                <td class="status text-center">
                                    <?php if ($g['status'] === 'published'): ?>
                                        <span class="status-badge badge-success">Đã xuất bản</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-secondary">Bản nháp</span>
                                    <?php endif; ?>
                                </td>                                <td class="actions text-center">
                                    <?php if ($canEditGuide): ?>
                                        <button class="btn-icon btn-edit" data-id="<?php echo $g['id']; ?>" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-toggle <?php echo $g['status']==='published' ? 'btn-success' : 'btn-secondary'; ?>" data-id="<?php echo $g['id']; ?>" data-status="<?php echo $g['status']==='published' ? 'draft' : 'published'; ?>" title="<?php echo $g['status']==='published' ? 'Chuyển sang Nháp' : 'Xuất bản'; ?>">
                                            <i class="fas fa-toggle-<?php echo $g['status']==='published' ? 'on' : 'off'; ?>"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" data-id="<?php echo $g['id']; ?>" data-title="<?php echo htmlspecialchars($g['title']); ?>" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-icon btn-view" data-id="<?php echo $g['id']; ?>" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Không có hướng dẫn nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php include $private_layouts_path . 'pagination.php'; ?>
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