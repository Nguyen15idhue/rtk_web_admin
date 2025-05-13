<?php
// filepath: public/pages/support/support_management.php
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$user_display_name = $bootstrap_data['user_display_name'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Use the calculated base_path for redirection
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$page_title = 'Chăm sóc khách hàng';
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <a href="<?php echo $base_url; ?>public/pages/setting/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_url; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>
    <div class="content-section">
        <div class="header-actions">
            <h3>Danh sách yêu cầu hỗ trợ</h3>
        </div>
        <form id="filterForm" class="filter-bar" onsubmit="return false;">
            <input type="search" id="searchInput" placeholder="Tìm kiếm...">
            <select id="categoryFilter">
                <option value="">Tất cả thể loại</option>
                <option value="technical">Kỹ thuật</option>
                <option value="billing">Thanh toán</option>
                <option value="account">Tài khoản</option>
                <option value="other">Khác</option>
            </select>
            <select id="statusFilter">
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ xử lý</option>
                <option value="in_progress">Đang xử lý</option>
                <option value="resolved">Đã giải quyết</option>
                <option value="closed">Đã đóng</option>
            </select>
        </form>

        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="support_requests">
            <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px;">
                <button type="submit" name="export_selected" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Xuất mục đã chọn
                </button>
                <button type="submit" name="export_all" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất tất cả
                </button>
            </div>

            <div class="transactions-table-wrapper">
                <table class="transactions-table" id="tbl-support">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Tiêu đề</th>
                            <th>Thể loại</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th class="actions text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</main>

<!-- Modal -->
<div id="supportModal" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="modal-close">&times;</span>
        <h3>Chi tiết yêu cầu hỗ trợ</h3>
        <p><strong>ID:</strong> <span id="modalId"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Tiêu đề:</strong> <span id="modalSubject"></span></p>
        <p><strong>Nội dung:</strong></p>
        <p id="modalMessage"></p>
        <p><strong>Thể loại:</strong> <span id="modalCategory"></span></p>
        <p><strong>Trạng thái:</strong>
            <select id="modalStatus" class="form-control">
                <option value="pending">Chờ xử lý</option>
                <option value="in_progress">Đang xử lý</option>
                <option value="resolved">Đã giải quyết</option>
                <option value="closed">Đã đóng</option>
            </select>
        </p>
        <p><strong>Phản hồi của Admin:</strong></p>
        <textarea id="modalResponse" rows="4"></textarea>
        <p><strong>Ngày tạo:</strong> <span id="modalCreated"></span></p>
        <p><strong>Ngày cập nhật:</strong> <span id="modalUpdated"></span></p>
        <button id="saveBtn" class="btn btn-primary">Lưu thay đổi</button>
    </div>
</div>

<script>
    window.basePath = '<?php echo rtrim($base_url, '/'); ?>';
</script>
<script defer src="<?php echo $base_url; ?>public/assets/js/pages/support/support_management.js"></script>

<?php include $private_layouts_path . 'admin_footer.php'; ?>
