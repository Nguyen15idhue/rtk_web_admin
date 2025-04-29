<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}
// --- Base Path & Includes ---
$protocol = (!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'||$_SERVER['SERVER_PORT']==443)?"https://":"http://";
$host = $_SERVER['HTTP_HOST'];
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$idx = array_search('rtk_web_admin',$parts);
$base_seg = $idx!==false? implode('/',array_slice($parts,0,$idx+1)).'/':'/';
$base_path = $protocol.$host.$base_seg;
$private_includes = __DIR__ . '/../../private/includes/';
$page_title = isset($_GET['id']) ? 'Sửa Guide' : 'Tạo Guide';
include $private_includes . 'admin_header.php';
include $private_includes . 'admin_sidebar.php';
?>
<!-- Thêm Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/forms.css">

<main class="content-wrapper">
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="mb-0"><?php echo $page_title; ?></h2>
            </div>
            <div class="card-body">
                <form id="frm-guide" class="row g-3" enctype="multipart/form-data"
                      data-base-path="<?php echo rtrim($base_path, '/'); ?>">
                    <input type="hidden" name="id" value="<?php echo intval($_GET['id'] ?? 0); ?>">
                    <input type="hidden" name="existing_thumbnail" value="">

                    <div class="col-12 mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Topic</label>
                        <input type="text" name="topic" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" name="image" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea id="guideContent" name="content" class="form-control" rows="8"></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='guide_management.php'">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Thêm jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Thêm TinyMCE -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
<!-- Thêm Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- include JS riêng cho edit_guide -->
<script src="<?php echo $base_path; ?>public/assets/js/pages/guide/edit_guide.js"></script>

<?php include $private_includes . 'admin_footer.php'; ?>
