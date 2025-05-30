<?php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$user_display_name = $bootstrap_data['user_display_name'];

// Redirect nếu chưa auth
if (empty($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
$id = intval($_GET['id'] ?? 0);
$viewMode = ($_GET['mode'] ?? '') === 'view';
$page_title = $id
    ? ($viewMode ? 'Xem hướng dẫn' : 'Chỉnh sửa hướng dẫn')
    : 'Tạo hướng dẫn mới';
?>
<!-- Thêm Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<main class="content-wrapper">
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="mb-0"><?php echo $page_title; ?></h2>
            </div>
            <div class="card-body">                <form id="frm-guide" class="row g-3" enctype="multipart/form-data" autocomplete="off"
                      data-base-path="<?php echo rtrim($base_url, '/'); ?>"
                      data-view-mode="<?php echo $viewMode ? 'true' : 'false'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($_GET['id'] ?? 0); ?>">
                    <input type="hidden" name="existing_thumbnail" value="">

                    <div class="col-12 mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" class="form-control" <?php echo $viewMode ? 'readonly' : 'required'; ?>>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" name="slug" class="form-control" <?php echo $viewMode ? 'readonly' : ''; ?>>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chủ đề</label>
                        <input type="text" name="topic" class="form-control" list="topicsList" placeholder="Chọn hoặc nhập chủ đề mới" autocomplete="off" <?php echo $viewMode ? 'readonly' : ''; ?>>
                        <datalist id="topicsList"></datalist>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" <?php echo $viewMode ? 'disabled' : ''; ?>>
                            <option value="draft">Bản nháp</option>
                            <option value="published">Đã xuất bản</option>
                            <option value="archived">Lưu trữ</option>
                        </select>
                    </div>                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="thumbnail" class="form-control" <?php echo $viewMode ? 'disabled' : ''; ?>>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea id="guideContent" name="content" class="form-control" rows="8" <?php echo $viewMode ? 'readonly' : ''; ?>></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='guide_management.php'">
                            <?php echo $viewMode ? 'Quay lại' : 'Hủy'; ?>
                        </button>
                        <?php if (!$viewMode): ?>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Thêm TinyMCE -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
<!-- Thêm Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- include JS riêng cho edit_guide -->
<script defer src="<?php echo $base_url; ?>public/assets/js/pages/guide/edit_guide.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>