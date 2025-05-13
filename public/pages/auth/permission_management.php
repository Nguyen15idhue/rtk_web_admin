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

// Load all defined permissions from config file
$all_defined_permissions = require_once __DIR__ . '/../../../private/config/app_permissions.php';
$roles_to_sync = ['admin', 'customercare']; // Define roles to sync permissions for

if ($db) {
    // Synchronize permissions: Add new permissions from config to DB if they don't exist
    $stmt_check_perm = $db->prepare("SELECT 1 FROM role_permissions WHERE role = :role AND permission = :permission");
    $stmt_add_perm = $db->prepare("INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :permission, 0)");

    foreach ($roles_to_sync as $role_to_sync) {
        foreach ($all_defined_permissions as $perm_code => $perm_description) {
            $stmt_check_perm->execute([':role' => $role_to_sync, ':permission' => $perm_code]);
            if ($stmt_check_perm->fetchColumn() === false) {
                // Permission does not exist for this role, add it with allowed = 0
                $stmt_add_perm->execute([':role' => $role_to_sync, ':permission' => $perm_code]);
            }
        }
    }
}

// Fetch current permissions from DB for all roles (potentially updated after sync)
$role_permissions_from_db = [];
if ($db) {
    $stmt = $db->query("SELECT role, permission, allowed FROM role_permissions");
    if ($stmt) {
        $raw_perms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($raw_perms as $p) {
            if (!isset($role_permissions_from_db[$p['role']])) {
                $role_permissions_from_db[$p['role']] = [];
            }
            $role_permissions_from_db[$p['role']][$p['permission']] = (bool)$p['allowed'];
        }
    }
}

// Prepare UI permissions map by merging defined permissions with DB state
$ui_permissions = [];
$roles_to_display = ['admin', 'customercare']; // Roles you want to manage permissions for

foreach ($roles_to_display as $role_key) {
    $ui_permissions[$role_key] = [];
    foreach ($all_defined_permissions as $perm_code => $perm_description) {
        $is_allowed = $role_permissions_from_db[$role_key][$perm_code] ?? false; // Default to false if not in DB
        $ui_permissions[$role_key][$perm_code] = [
            'description' => $perm_description,
            'allowed' => $is_allowed,
        ];
    }
}

// Define permission groups for UI display
$permission_groups_config = [
    'Tổng quan & Hệ thống Căn bản' => ['dashboard', 'settings'],
    'Quản lý Truy cập & Quản trị viên' => ['permission_management', 'permission_edit', 'admin_user_create'],
    'Quản lý Người dùng (Khách hàng)' => ['user_management', 'user_create'],
    'Quản lý Tài khoản Đo đạc' => ['account_management'],
    'Quản lý Tài chính & Giao dịch' => ['invoice_management', 'invoice_review', 'revenue_management', 'voucher_management'],
    'Quản lý Nội dung & Trạm' => ['guide_management', 'station_management'],
    'Hỗ trợ & Giới thiệu' => ['support_management', 'referral_management'],
    'Báo cáo' => ['reports']
];
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
                            <div class="text-xs max-h-40 sm:max-h-48 overflow-y-auto pr-2 border-t border-b py-2 my-2">
                                <?php
                                $rendered_admin_permissions = [];
                                $admin_group_idx = 0;
                                foreach ($permission_groups_config as $group_title => $perm_codes_in_group):
                                    $admin_group_idx++;
                                    $admin_group_content_id = "admin-group-content-{$admin_group_idx}";
                                    $group_has_visible_perms = false;
                                    ob_start(); // Start output buffering to capture group's permissions
                                    foreach ($perm_codes_in_group as $perm_code):
                                        if (isset($ui_permissions['admin'][$perm_code])):
                                            $perm_data = $ui_permissions['admin'][$perm_code];
                                            $is_core_admin_perm = ($perm_code === 'dashboard' || $perm_code === 'permission_management');
                                            $final_checked_state = $is_core_admin_perm ? true : $perm_data['allowed'];
                                            $final_disabled_state = $is_core_admin_perm;
                                            $rendered_admin_permissions[$perm_code] = true;
                                            $group_has_visible_perms = true;
                                ?>
                                <label class="flex items-center py-1">
                                    <input type="checkbox"
                                           class="mr-2 h-3 w-3 accent-primary-600"
                                           data-role="Admin"
                                           data-permission="<?= htmlspecialchars($perm_code) ?>"
                                           <?= $final_checked_state ? 'checked' : '' ?>
                                           <?= $final_disabled_state ? 'disabled' : '' ?>
                                    > <?= htmlspecialchars($perm_data['description']) ?>
                                </label>
                                <?php
                                        endif;
                                    endforeach;
                                    $group_permissions_html = ob_get_clean(); // Get buffered output
                                    if ($group_has_visible_perms):
                                ?>
                                <div class="permission-group mt-1 first:mt-0">
                                    <h5 class="permission-group-header font-semibold text-gray-700 text-xs mb-1 cursor-pointer flex justify-between items-center py-1" 
                                        onclick="togglePermissionGroup(this, '<?= $admin_group_content_id ?>')">
                                        <span><?= htmlspecialchars($group_title) ?></span>
                                        <i class="fas fa-chevron-down text-gray-500"></i>
                                    </h5>
                                    <div id="<?= $admin_group_content_id ?>" class="permission-group-content pl-3 space-y-1" style="display: none;">
                                        <?= $group_permissions_html ?>
                                    </div>
                                </div>
                                <?php
                                    endif;
                                endforeach;

                                // Display any permissions not in defined groups
                                $other_perms_html = '';
                                ob_start();
                                $has_other_perms = false;
                                foreach ($ui_permissions['admin'] as $perm_code => $perm_data):
                                    if (!isset($rendered_admin_permissions[$perm_code])):
                                        $has_other_perms = true;
                                        $is_core_admin_perm = ($perm_code === 'dashboard' || $perm_code === 'permission_management');
                                        $final_checked_state = $is_core_admin_perm ? true : $perm_data['allowed'];
                                        $final_disabled_state = $is_core_admin_perm;
                                ?>
                                <label class="flex items-center py-1">
                                    <input type="checkbox"
                                           class="mr-2 h-3 w-3 accent-primary-600"
                                           data-role="Admin"
                                           data-permission="<?= htmlspecialchars($perm_code) ?>"
                                           <?= $final_checked_state ? 'checked' : '' ?>
                                           <?= $final_disabled_state ? 'disabled' : '' ?>
                                    > <?= htmlspecialchars($perm_data['description']) ?>
                                </label>
                                <?php
                                    endif;
                                endforeach;
                                $other_perms_html = ob_get_clean();
                                if ($has_other_perms):
                                    $admin_group_idx++;
                                    $admin_other_group_content_id = "admin-group-content-{$admin_group_idx}";
                                ?>
                                <div class="permission-group mt-1">
                                    <h5 class="permission-group-header font-semibold text-gray-700 text-xs mb-1 cursor-pointer flex justify-between items-center py-1"
                                        onclick="togglePermissionGroup(this, '<?= $admin_other_group_content_id ?>')">
                                        <span>Quyền Khác</span>
                                        <i class="fas fa-chevron-down text-gray-500"></i>
                                    </h5>
                                    <div id="<?= $admin_other_group_content_id ?>" class="permission-group-content pl-3 space-y-1" style="display: none;">
                                        <?= $other_perms_html ?>
                                    </div>
                                </div>
                                <?php endif; ?>
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
                        <div class="text-xs max-h-40 sm:max-h-48 overflow-y-auto pr-2 border-t border-b py-2 my-2">
                            <?php
                                $rendered_cskh_permissions = [];
                                $cskh_group_idx = 0;
                                foreach ($permission_groups_config as $group_title => $perm_codes_in_group):
                                    $cskh_group_idx++;
                                    $cskh_group_content_id = "cskh-group-content-{$cskh_group_idx}";
                                    $group_has_visible_perms_cskh = false;
                                    ob_start();
                                    foreach ($perm_codes_in_group as $perm_code):
                                        if (isset($ui_permissions['customercare'][$perm_code])):
                                            $perm_data = $ui_permissions['customercare'][$perm_code];
                                            $rendered_cskh_permissions[$perm_code] = true;
                                            $group_has_visible_perms_cskh = true;
                            ?>
                            <label class="flex items-center py-1">
                                <input type="checkbox"
                                       class="mr-2 h-3 w-3 accent-primary-600"
                                       data-role="CustomerCare"
                                       data-permission="<?= htmlspecialchars($perm_code) ?>"
                                       <?= $perm_data['allowed'] ? 'checked' : '' ?>
                                > <?= htmlspecialchars($perm_data['description']) ?>
                            </label>
                            <?php
                                        endif;
                                    endforeach;
                                    $group_permissions_html_cskh = ob_get_clean();
                                    if ($group_has_visible_perms_cskh):
                            ?>
                            <div class="permission-group mt-1 first:mt-0">
                                <h5 class="permission-group-header font-semibold text-gray-700 text-xs mb-1 cursor-pointer flex justify-between items-center py-1"
                                    onclick="togglePermissionGroup(this, '<?= $cskh_group_content_id ?>')">
                                    <span><?= htmlspecialchars($group_title) ?></span>
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </h5>
                                <div id="<?= $cskh_group_content_id ?>" class="permission-group-content pl-3 space-y-1" style="display: none;">
                                    <?= $group_permissions_html_cskh ?>
                                </div>
                            </div>
                            <?php
                                    endif;
                                endforeach;

                                // Display any permissions not in defined groups for CustomerCare
                                $other_perms_cskh_html = '';
                                ob_start();
                                $has_other_perms_cskh = false;
                                foreach ($ui_permissions['customercare'] as $perm_code => $perm_data):
                                    if (!isset($rendered_cskh_permissions[$perm_code])):
                                        $has_other_perms_cskh = true;
                            ?>
                            <label class="flex items-center py-1">
                                <input type="checkbox"
                                       class="mr-2 h-3 w-3 accent-primary-600"
                                       data-role="CustomerCare"
                                       data-permission="<?= htmlspecialchars($perm_code) ?>"
                                       <?= $perm_data['allowed'] ? 'checked' : '' ?>
                                > <?= htmlspecialchars($perm_data['description']) ?>
                            </label>
                            <?php
                                    endif;
                                endforeach;
                                $other_perms_cskh_html = ob_get_clean();
                                if ($has_other_perms_cskh):
                                    $cskh_group_idx++;
                                    $cskh_other_group_content_id = "cskh-group-content-{$cskh_group_idx}";
                            ?>
                            <div class="permission-group mt-1">
                                 <h5 class="permission-group-header font-semibold text-gray-700 text-xs mb-1 cursor-pointer flex justify-between items-center py-1"
                                    onclick="togglePermissionGroup(this, '<?= $cskh_other_group_content_id ?>')">
                                    <span>Quyền Khác</span>
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </h5>
                                <div id="<?= $cskh_other_group_content_id ?>" class="permission-group-content pl-3 space-y-1" style="display: none;">
                                    <?= $other_perms_cskh_html ?>
                                </div>
                            </div>
                            <?php endif; ?>
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

<script>
function togglePermissionGroup(headerElement, contentId) {
    const contentElement = document.getElementById(contentId);
    const icon = headerElement.querySelector('i.fas');
    if (contentElement) {
        const isHidden = contentElement.style.display === 'none' || contentElement.style.display === '';
        contentElement.style.display = isHidden ? 'block' : 'none';
        if (icon) {
            if (isHidden) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }
}
</script>

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