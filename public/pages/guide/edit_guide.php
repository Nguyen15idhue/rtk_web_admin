<?php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$user_display_name = $bootstrap_data['user_display_name'];
// Simplified base URL without trailing slash for reuse
$basePath = rtrim($base_url, '/');

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

// Simplify icon, description, and input attributes
if (!$id) {
    $iconBg = 'primary';
    $iconClass = 'fas fa-plus';
    $page_desc = 'Tạo hướng dẫn mới để hỗ trợ người dùng';
} elseif ($viewMode) {
    $iconBg = 'info';
    $iconClass = 'fas fa-eye';
    $page_desc = 'Xem chi tiết thông tin hướng dẫn';
} else {
    $iconBg = 'warning';
    $iconClass = 'fas fa-edit';
    $page_desc = 'Cập nhật thông tin hướng dẫn';
}
$readonlyAttr = $viewMode ? 'readonly' : '';
$disabledAttr = $viewMode ? 'disabled' : '';
$requiredAttr = $viewMode ? '' : 'required';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="<?php echo $base_url; ?>public/assets/css/pages/guide/edit_guide.css" rel="stylesheet">

<main class="content-wrapper">
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-<?php echo $iconBg; ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="<?php echo $iconClass; ?> text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-1"><?php echo $page_title; ?></h4>
                        <p class="text-muted mb-0 small"><?php echo $page_desc; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="frm-guide" class="row g-3" enctype="multipart/form-data" autocomplete="off"
                      data-base-path="<?php echo $basePath; ?>"
                      data-view-mode="<?php echo $viewMode ? 'true' : 'false'; ?>">
                    <input type="hidden" name="id" value="<?php echo intval($_GET['id'] ?? 0); ?>">
                    <input type="hidden" name="action" value="save_guide">
                    <input type="hidden" name="existing_thumbnail" value="">
                    <input type="hidden" name="draft_id" value="">

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-heading text-primary me-2"></i>Tiêu đề
                        </label>
                        <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề hướng dẫn..." <?php echo trim("$readonlyAttr $requiredAttr"); ?>>
                    </div>

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
                                <input type="text" name="topic" class="form-control" list="topicsList" placeholder="Chọn chủ đề..." autocomplete="off" <?php echo $readonlyAttr; ?>>
                                <datalist id="topicsList"></datalist>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-toggle-on text-success me-2"></i>Trạng thái
                                </label>
                                <select name="status" class="form-select" <?php echo $disabledAttr; ?>>
                                    <option value="draft">📝 Bản nháp</option>
                                    <option value="published">✅ Đã xuất bản</option>
                                    <option value="archived">📦 Lưu trữ</option>
                                </select>
                            </div>
                        </div>
                    </div>

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

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-file-alt text-dark me-2"></i>Nội dung
                        </label>
                        <div class="content-editor-wrapper">
                            <textarea id="guideContent" name="content" class="form-control" rows="6" placeholder="Nhập nội dung hướng dẫn chi tiết..." <?php echo $readonlyAttr; ?>></textarea>
                        </div>
                    </div>

                    <?php if (!$viewMode): ?>
                    <div class="col-12">
                        <div class="auto-save-status d-flex align-items-center p-2 bg-light rounded">
                            <div class="auto-save-indicator me-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status" id="autoSaveSpinner" style="display: none;">
                                    <span class="visually-hidden">Đang lưu...</span>
                                </div>
                                <i class="fas fa-check-circle text-success" id="autoSaveSuccess" style="display: none;"></i>
                                <i class="fas fa-exclamation-triangle text-warning" id="autoSaveError" style="display: none;"></i>
                            </div>
                            <span id="autoSaveText" class="small text-muted">Tự động lưu nháp sau 30 giây không tương tác</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <?php if (!$viewMode): ?>
                                <button type="button" class="btn btn-outline-info btn-sm d-none d-md-inline-flex" onclick="showKeyboardShortcutsHelp()" title="Phím tắt (Ctrl+Shift+H)">
                                    <i class="fas fa-keyboard me-1"></i>Phím tắt
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="window.location.href='guide_management.php'">
                                    <i class="fas fa-arrow-left me-1"></i><?php echo $viewMode ? 'Quay lại' : 'Hủy'; ?>
                                </button>
                                <?php if (!$viewMode): ?>
                                    <button type="button" class="btn btn-outline-warning px-3 me-2" id="previewBtn">
                                        <i class="fas fa-eye me-1"></i>Xem trước
                                    </button>
                                    <button type="submit" class="btn btn-primary px-3">
                                        <i class="fas fa-save me-1"></i>Lưu hướng dẫn
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/tinymce@7.4.1/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Xem trước bài viết
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="background-color: #f8f9fa;">
                <div class="guide-detail-container">
                    <div class="guide-title" id="previewTitle">Tiêu đề bài viết</div>
                    <div class="guide-meta" id="previewMeta">
                        <span>Chủ đề: <span id="previewTopic">Chưa chọn</span></span> |
                        <span>Ngày đăng: <span id="previewDate"></span></span>
                    </div>
                    <img class="guide-thumbnail" id="previewThumbnail" src="" alt="Thumbnail" style="display: none;">
                    <div class="guide-content" id="previewContent">
                        <p>Nội dung bài viết sẽ hiển thị ở đây...</p>
                    </div>
                    <div class="guide-back-container">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            ← Đóng xem trước
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.basePath = '<?php echo $basePath; ?>/';
</script>
<script defer src="<?php echo $basePath; ?>/public/assets/js/pages/guide/edit_guide.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>