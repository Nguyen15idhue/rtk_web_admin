<?php
$bootstrap = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$db = $bootstrap['db'];
$base_path = $bootstrap['base_path'];
$base_url = $bootstrap['base_url'];
$user_display_name = $bootstrap['user_display_name'];
$private_layouts_path = $bootstrap['private_layouts_path'];
$is_admin = ($_SESSION['admin_role'] ?? '') === 'admin';
$admins = $db ? $db->query("SELECT id,name,admin_username,role,created_at FROM admin")->fetchAll(PDO::FETCH_ASSOC) : [];
$nav = ['pages/setting/profile.php' => 'Hồ sơ', 'pages/auth/admin_logout.php' => 'Đăng xuất'];
?>

<?php include $private_layouts_path . 'admin_sidebar.php'; include $private_layouts_path . 'admin_header.php'; ?>

<main class="content-wrapper">
    <div class="content-header">
        <h2 class="text-2xl font-semibold">Quản lý Phân quyền</h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?= htmlspecialchars($user_display_name) ?></span>!</span>
            <?php foreach ($nav as $u => $t): ?>
                <a href="<?= $base_url ?>public/<?= $u ?>"><?= $t ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="admin-permission-management" class="content-section">
        <div class="flex flex-row justify-between items-center mb-4 gap-3 md:gap-2">
            <h3 class="text-lg md:text-xl font-semibold text-gray-900">Quản lý phân quyền</h3>
            <button class="btn btn-primary self-start md:self-auto w-auto" onclick="PermissionPageEvents.openCreateRoleModal()" data-permission="admin_user_create">
                <i class="fas fa-user-plus mr-1"></i> Thêm QTV/Vận hành
            </button>
        </div>
        <p class="text-xs sm:text-sm text-gray-600 mb-6">Quản lý vai trò và quyền hạn truy cập cho tài khoản quản trị hệ thống.</p>

        <!-- Permission Cards: Responsive Grid -->
        <div class="stats-grid"> 

            <!-- Admin stat card -->
            <div class="stat-card">
                <div class="icon bg-blue-200 text-blue-600"><i class="fas fa-user-shield"></i></div>
                <div>
                    <h3>Quản trị viên</h3>
                    <p>Quản lý hoạt động hàng ngày.</p>
                    <form>
                        <fieldset disabled style="border:none;">
                            <div class="space-y-2 text-xs max-h-40 sm:max-h-48 overflow-y-auto pr-2 border-t border-b py-2 my-2">
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked disabled> Dashboard
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Admin" data-permission="user_management"> QL User (KH)
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Admin" data-permission="account_management"> QL TK Đo đạc
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Admin" data-permission="invoice_management"> QL Giao dịch
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Admin" data-permission="referral_management"> QL Giới thiệu
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Admin" data-permission="reports"> Xem Báo cáo
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" data-role="Admin" data-permission="revenue_management"> QL Doanh thu
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-2 h-3 w-3" disabled data-role="Admin" data-permission="permission_management"> QL Phân quyền
                                </label>
                            </div>
                            <button type="button" class="btn btn-primary mt-2 text-xs" disabled>
                                Lưu quyền Admin
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>

            <!-- Customer Care stat card -->
            <div class="stat-card">
                <div class="icon bg-green-200 text-green-600"><i class="fas fa-headset"></i></div>
                <div>
                    <h3>Chăm sóc khách hàng</h3>
                    <p>Chỉ xem và hỗ trợ cơ bản.</p>
                    <form>
                        <div class="space-y-2 text-xs max-h-40 sm:max-h-48 overflow-y-auto pr-2 border-t border-b py-2 my-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" data-role="CustomerCare" data-permission="dashboard" disabled> Dashboard
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" data-role="CustomerCare" data-permission="user_management"> QL User (Xem)
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" data-role="CustomerCare" data-permission="invoice_management"> QL Giao dịch
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary mt-2 text-xs" onclick="PermissionPageEvents.savePermissions('CustomerCare', event)" data-permission="permission_edit">
                            Lưu quyền CSKH
                        </button>
                    </form>
                </div>
            </div>

        </div> <!-- end stats-grid -->
        <p class="text-xs text-red-600 mt-4 italic">*Lưu ý: Chỉ Admin có thể thay đổi quyền. TK mới tạo cần đổi MK mặc định.</p>
    </div>

    <!-- Admin Accounts List -->
    <div class="content-section">
        <h3>Danh sách tài khoản quản trị</h3>
        <div class="transactions-table-wrapper">
            <table class="transactions-table" id="adminAccountsTable">
                <thead>
                    <tr>
                        <th>ID</th><th>Tên</th><th>Username</th><th>Vai trò</th><th>Ngày tạo</th><th class="actions" style="text-align:center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id']) ?></td>
                        <td><?= htmlspecialchars($a['name']) ?></td>
                        <td><?= htmlspecialchars($a['admin_username']) ?></td>
                        <td><?= ($a['role'] === 'customercare' ? 'Chăm sóc khách hàng' : 'Quản trị viên') ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td class="actions">
                            <button class="btn btn-secondary btn-sm" onclick="PermissionPageEvents.openEditAdminModal(<?= $a['id'] ?>)">Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="PermissionPageEvents.openDeleteAdminModal(<?= $a['id'] ?>)">Xóa</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Export PHP vars and load external JS -->
<script>
    window.basePath        = '<?= $base_path ?>';
    window.adminsData      = <?= json_encode($admins) ?>;
    window.isAdmin    = <?= json_encode($is_admin) ?>;
</script>
<script defer src="<?= $base_url ?>public/assets/js/pages/auth/permission_management.js"></script>

<!-- Create Admin/Operator Modal -->
<div id="createRoleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Thêm QTV/Vận hành</h4>
        </div>
        <form id="createRoleForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="roleName">Tên:</label>
                    <input type="text" id="roleName" name="name" required class="w-full">
                </div>
                <div class="form-group">
                    <label for="roleUsername">Tên đăng nhập:</label>
                    <input type="text" id="roleUsername" name="username" required class="w-full">
                </div>
                <div class="form-group">
                    <label for="rolePassword">Mật khẩu:</label>
                    <input type="password" id="rolePassword" name="password" required class="w-full">
                </div>
                <div class="form-group">
                    <label for="roleType">Vai trò:</label>
                    <select id="roleType" name="role" required class="w-full">
                        <option value="">Chọn vai trò</option>
                        <option value="admin">Quản trị viên</option>
                        <option value="customercare">Chăm sóc khách hàng</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="PermissionPageEvents.closeModal('createRoleModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Admin Modal -->
<div id="editAdminModal" class="modal">
    <div class="modal-content">
        <form id="editAdminForm">
            <div class="modal-header">
                <h4>Chỉnh sửa Admin</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editAdminId" name="id">
                <div class="form-group">
                    <label for="editAdminName">Tên:</label>
                    <input type="text" id="editAdminName" name="name" required class="w-full">
                </div>
                <div class="form-group">
                    <label for="editAdminUsername">Username:</label>
                    <input type="text" id="editAdminUsername" readonly class="w-full form-input">
                </div>
                <div class="form-group">
                    <label for="editAdminPassword">Mật khẩu mới:</label>
                    <input type="password" id="editAdminPassword" name="password" class="w-full form-input">
                </div>
                <div class="form-group">
                    <label for="editAdminRole">Vai trò:</label>
                    <select id="editAdminRole" name="role" required class="w-full form-input">
                        <option value="admin">Quản trị viên</option>
                        <option value="customercare">Chăm sóc khách hàng</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="PermissionPageEvents.closeModal('editAdminModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Admin Modal -->
<div id="deleteAdminModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Xác nhận xóa</h4>
        </div>
        <div class="modal-body">
            <p>Bạn có chắc muốn xóa tài khoản này?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="PermissionPageEvents.closeModal('deleteAdminModal')">Hủy</button>
            <button id="confirmDeleteAdminBtn" class="btn btn-danger">Xóa</button>
        </div>
    </div>
</div>
<?php
include $private_layouts_path . 'admin_footer.php';
?>