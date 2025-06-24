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
<!-- Thêm Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- CSS riêng cho edit guide -->
<link href="<?php echo $base_url; ?>public/assets/css/pages/guide/edit_guide.css" rel="stylesheet">

<main class="content-wrapper">
    <div class="container py-4">
        <div class="card shadow-sm mb-4">            <div class="card-header bg-white">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <?php if (!$id): ?>
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                        <?php elseif ($viewMode): ?>
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-eye text-white"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="mb-1"><?php echo $page_title; ?></h4>
                        <p class="text-muted mb-0 small">
                            <?php if (!$id): ?>
                                Tạo hướng dẫn mới để hỗ trợ người dùng
                            <?php elseif ($viewMode): ?>
                                Xem chi tiết thông tin hướng dẫn
                            <?php else: ?>
                                Cập nhật thông tin hướng dẫn
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">                <form id="frm-guide" class="row g-3" enctype="multipart/form-data" autocomplete="off"
                      data-base-path="<?php echo rtrim($base_url, '/'); ?>"
                      data-view-mode="<?php echo $viewMode ? 'true' : 'false'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($_GET['id'] ?? 0); ?>">
                    <input type="hidden" name="existing_thumbnail" value="">

                    <!-- Tiêu đề -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-heading text-primary me-2"></i>Tiêu đề
                        </label>
                        <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề hướng dẫn..." <?php echo $viewMode ? 'readonly' : 'required'; ?>>
                    </div>                    <!-- Row chính với layout cân đối -->
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-link text-secondary me-2"></i>Đường dẫn tĩnh (Slug)
                                </label>
                                <input type="text" name="slug" class="form-control" placeholder="Tự động tạo từ tiêu đề..." readonly>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-tag text-info me-2"></i>Chủ đề
                                </label>
                                <input type="text" name="topic" class="form-control" list="topicsList" placeholder="Chọn chủ đề..." autocomplete="off" <?php echo $viewMode ? 'readonly' : ''; ?>>
                                <datalist id="topicsList"></datalist>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-toggle-on text-success me-2"></i>Trạng thái
                                </label>
                                <select name="status" class="form-select" <?php echo $viewMode ? 'disabled' : ''; ?>>
                                    <option value="draft">📝 Bản nháp</option>
                                    <option value="published">✅ Đã xuất bản</option>
                                    <option value="archived">📦 Lưu trữ</option>
                                </select>
                            </div>
                        </div>
                    </div>                    <!-- Ảnh đại diện -->
                    <div class="col-lg-4 d-flex flex-column">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-image text-warning me-2"></i>Ảnh đại diện
                        </label>                        
                        <div class="thumbnail-upload-container mx-auto">
                            <div class="thumbnail-preview compact" id="thumbnailPreview" onclick="<?php echo !$viewMode ? 'document.getElementById(\'thumbnailInput\').click()' : ''; ?>">
                                <div class="preview-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p class="mb-0 small">Nhấp để chọn ảnh</p>
                                </div>
                                <?php if (!$viewMode): ?>
                                    <div class="thumbnail-overlay" id="thumbnailOverlay" style="display: none;">
                                        <button type="button" class="btn btn-light btn-sm me-2" onclick="event.stopPropagation(); document.getElementById('thumbnailInput').click()">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="removeThumbnail" onclick="event.stopPropagation();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!$viewMode): ?>
                                <input type="file" name="thumbnail" id="thumbnailInput" class="d-none" accept="image/*">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Nội dung -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-file-alt text-dark me-2"></i>Nội dung
                        </label>
                        <div class="content-editor-wrapper">
                            <textarea id="guideContent" name="content" class="form-control" rows="6" placeholder="Nhập nội dung hướng dẫn chi tiết..." <?php echo $viewMode ? 'readonly' : ''; ?>></textarea>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="window.location.href='guide_management.php'">
                                <i class="fas fa-arrow-left me-1"></i><?php echo $viewMode ? 'Quay lại' : 'Hủy'; ?>
                            </button>
                            <?php if (!$viewMode): ?>
                                <button type="submit" class="btn btn-primary px-3">
                                    <i class="fas fa-save me-1"></i>Lưu hướng dẫn
                                </button>
                            <?php endif; ?>
                        </div>
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