<?php
$bootstrap = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$page_title = 'Quản lý Phân quyền';
$db = $bootstrap['db'];
$base_path = $bootstrap['base_path'];
$base_url = $bootstrap['base_url'];
$user_display_name = $bootstrap['user_display_name'];
$private_layouts_path = $bootstrap['private_layouts_path'];
$is_admin = ($_SESSION['admin_role'] ?? '') === 'admin';

// --- NEW: Get permission status for editing permissions ---
$canEditPermissions = Auth::can('permission_management_edit');

$admins = $db ? $db->query("SELECT id,name,admin_username,role,created_at FROM admin")->fetchAll(PDO::FETCH_ASSOC) : [];
$nav = ['pages/setting/profile.php' => 'Hồ sơ', 'pages/auth/admin_logout.php' => 'Đăng xuất'];

// Initialize and populate custom_role_display_names
$custom_role_display_names = [];
if ($db) {
    $stmt_custom_names = $db->query("SELECT role_key, role_display_name FROM custom_roles");
    if ($stmt_custom_names) {
        while ($row = $stmt_custom_names->fetch(PDO::FETCH_ASSOC)) {
            $custom_role_display_names[$row['role_key']] = $row['role_display_name'];
        }
    }
}

// Load all defined permissions from config file
$all_defined_permissions = require_once __DIR__ . '/../../../private/config/app_permissions.php';

if ($db) {
    // Synchronize permissions:
    // 1. Get all distinct roles from DB
    $stmt_get_roles = $db->query("SELECT DISTINCT role FROM role_permissions");
    $existing_roles_in_db = $stmt_get_roles ? $stmt_get_roles->fetchAll(PDO::FETCH_COLUMN) : [];

    // 2. Ensure 'admin' and 'customercare' are in the list, then add any other roles from DB
    $roles_to_process = array_unique(array_merge(['admin', 'customercare'], $existing_roles_in_db));

    // 3. For each role, ensure all defined permissions exist, add if not (default to allowed=0)
    $stmt_check_perm = $db->prepare("SELECT 1 FROM role_permissions WHERE role = :role AND permission = :permission");
    $stmt_add_perm = $db->prepare("INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :permission, 0)");

    foreach ($roles_to_process as $role_to_sync) {
        if (empty($role_to_sync)) continue; // Skip if role name is empty
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
// Fetch all distinct roles again to be displayed, ensuring admin and customercare are first if they exist
$stmt_display_roles = $db ? $db->query("SELECT DISTINCT role FROM role_permissions ORDER BY CASE role WHEN 'admin' THEN 1 WHEN 'customercare' THEN 2 ELSE 3 END, role ASC") : null;
$roles_to_display = $stmt_display_roles ? $stmt_display_roles->fetchAll(PDO::FETCH_COLUMN) : ['admin', 'customercare'];
if (empty($roles_to_display)) $roles_to_display = ['admin', 'customercare']; // Fallback if DB is empty

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

// Define permission groups for UI display (reordered to match sidebar)
$permission_groups_config = [
    'Quản lý Hệ thống' => [
        'station_management_view', 'station_management_edit',
        'voucher_management_view', 'voucher_management_edit',
        'permission_management_view', 'permission_management_edit'
    ],
    'Quản lý Người dùng & Tài khoản' => [
        'user_management_view', 'user_management_edit',
        'account_management_view', 'account_management_edit'
    ],
    'Khách hàng & Hỗ trợ' => [
        'referral_management_view', 'referral_management_edit',
        'support_management_view', 'support_management_edit',
        'guide_management_view', 'guide_management_edit'
    ],
    'Quản lý Tài chính & Giao dịch' => [
        'invoice_management_view', 'invoice_management_edit',
        'revenue_management_view', 'revenue_management_edit',
        'invoice_review_view', 'invoice_review_edit'
    ],
    'Báo cáo' => [
        'reports_view'
    ],
];

// Build pages-by-group for create-role modal
$pagesByGroup = [];
foreach ($permission_groups_config as $groupTitle => $codes) {
    $seen = [];
    foreach ($codes as $code) {
        // Lấy base, viewCode, editCode
        if (!isset($all_defined_permissions[$code])) continue;
        if (preg_match('/(.+?)_(view|edit)$/', $code, $m)) {
            $base = $m[1];
        } else {
            $base = $code;
        }
        if (in_array($base, $seen)) continue;
        $seen[] = $base;
        $viewCode = isset($all_defined_permissions[$base . '_view']) ? $base . '_view' : null;
        $editCode = isset($all_defined_permissions[$base . '_edit']) ? $base . '_edit' : null;
        $label = $all_defined_permissions[$viewCode] ?? $all_defined_permissions[$editCode] ?? $all_defined_permissions[$base] ?? $base;
        $pagesByGroup[$groupTitle][] = compact('base','viewCode','editCode','label');
    }
}

// Helper function to get a display name for a role key
function getRoleDisplayName($role_key) {
    global $custom_role_display_names; // This is already populated from custom_roles table earlier in the script
    if (isset($custom_role_display_names[$role_key])) {
        return $custom_role_display_names[$role_key];
    }
    // Fallback if not in custom_roles
    return ucfirst(str_replace('_', ' ', $role_key));
}
?>

<?php include $private_layouts_path . 'admin_sidebar.php'; include $private_layouts_path . 'admin_header.php'; ?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <div id="admin-permission-management" class="content-section">
        <div class="flex flex-row justify-between items-center mb-4 gap-3 md:gap-2">
            <h3 class="text-lg md:text-xl font-semibold text-gray-900">Quản lý phân quyền</h3>
            <?php if ($canEditPermissions): ?>
            <div class="flex gap-2">
                <button class="btn btn-success self-start md:self-auto w-auto" onclick="PermissionPageEvents.openCreateCustomRoleModal()" data-permission="permission_management">
                    <i class="fas fa-plus-circle mr-1"></i> Tạo Vai trò Mới
                </button>
                <button class="btn btn-primary self-start md:self-auto w-auto" onclick="PermissionPageEvents.openCreateRoleModal()" data-permission="admin_user_create">
                    <i class="fas fa-user-plus mr-1"></i> Tạo Tài khoản Mới
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Role Selection Area -->
        <div class="mb-6">
            <h4 class="text-md font-semibold mb-3 text-gray-700">Chọn Vai Trò để xem và cấu hình:</h4>
            <div id="role-selection-tabs" class="flex flex-wrap space-x-1 sm:space-x-2 border-b border-gray-300 pb-2">
                <?php
                $role_display_names_for_js = [];
                foreach ($roles_to_display as $role_key) {
                    if (empty($role_key)) continue;
                    $role_display_names_for_js[$role_key] = getRoleDisplayName($role_key);
                ?>
                    <button class="role-tab-button py-2 px-3 sm:px-4 text-xs sm:text-sm font-medium text-gray-600 hover:text-blue-700 hover:border-blue-600 border-b-2 border-transparent focus:outline-none transition-colors duration-150 ease-in-out" data-role-key="<?= htmlspecialchars($role_key) ?>">
                        <?= htmlspecialchars($role_display_names_for_js[$role_key]) ?>
                    </button>
                <?php } ?>
            </div>
        </div>
         <p class="text-xs text-red-600 mt-4 italic">*Lưu ý: Tài khoản mới tạo cần được yêu cầu đổi mật khẩu mặc định.</p>
    </div>

    <!-- Permissions Configuration Modal -->
    <div id="permissionsConfigModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Cấu hình quyền cho vai trò: <span id="modalRoleName"></span></h4>
                <span class="modal-close" onclick="PermissionPageEvents.closePermissionsModal()">&times;</span>
            </div>
            <div class="modal-body" id="permissionsModalBody" style="max-height:65vh; overflow-y:auto;">
                <!-- Content injected by JS -->
            </div>
            <div class="modal-footer" style="display:flex; justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="PermissionPageEvents.closePermissionsModal()" style="margin-right:8px;">Hủy</button>
                <button type="button" class="btn btn-primary" id="saveRolePermissionsBtn" <?php echo !$canEditPermissions ? 'disabled title="Bạn không có quyền thực hiện hành động này." style="cursor:not-allowed;"' : ''; ?>>Lưu thay đổi</button>
            </div>
        </div>
    </div>
    <!-- Admin Accounts List -->
    <div class="content-section">
        <h3>Danh sách tài khoản quản trị</h3>
        <div class="table-wrapper">
            <table class="table" id="adminAccountsTable">
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
                        <td><?= htmlspecialchars(getRoleDisplayName($a['role'])) ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td class="actions">
                            <?php if ($canEditPermissions): ?>
                            <button class="btn btn-secondary btn-sm" onclick="PermissionPageEvents.openEditAdminModal(<?= $a['id'] ?>)">Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="PermissionPageEvents.openDeleteAdminModal(<?= $a['id'] ?>)">Xóa</button>
                            <?php else: ?>
                            <span class="text-muted">Không có quyền</span>
                            <?php endif; ?>
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
    window.isAdmin         = <?= json_encode($is_admin) ?>;
    window.appConfig = {
        permissions: {
            permission_management_edit: <?= json_encode($canEditPermissions) ?>
        }
    };
    window.allDefinedPermissions = <?= json_encode($all_defined_permissions) ?>;
    window.permissionGroupsConfig = <?= json_encode($permission_groups_config) ?>;
    window.currentRolePermissions = <?= json_encode($ui_permissions) ?>;
    window.roleDisplayNames = <?= json_encode($role_display_names_for_js ?? []) ?>;
</script>
<script defer src="<?= $base_url ?>public/assets/js/pages/auth/permission_management.js"></script>

<!-- Create Admin/Operator Modal -->
<div id="createRoleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Tạo Tài khoản Mới</h4>
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
                        <?php foreach ($roles_to_display as $role_key_option): if(empty($role_key_option)) continue; ?>
                        <option value="<?= htmlspecialchars($role_key_option) ?>"><?= htmlspecialchars(getRoleDisplayName($role_key_option)) ?></option>
                        <?php endforeach; ?>
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
                        <?php foreach ($roles_to_display as $role_key_option): if(empty($role_key_option)) continue; ?>
                        <option value="<?= htmlspecialchars($role_key_option) ?>"><?= htmlspecialchars(getRoleDisplayName($role_key_option)) ?></option>
                        <?php endforeach; ?>
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
            <p>Bạn có chắc muốn xóa tài khoản <strong id="deleteAdminName"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="PermissionPageEvents.closeModal('deleteAdminModal')">Hủy</button>
            <button id="confirmDeleteAdminBtn" class="btn btn-danger">Xóa</button>
        </div>
    </div>
</div>

<!-- Create Custom Role Modal -->
<div id="createCustomRoleModal" class="modal">
    <div class="modal-content w-1/2 max-w-lg">
        <div class="modal-header">
            <h4>Tạo Vai trò Mới</h4>
        </div>
        <form id="createCustomRoleForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="customRoleName">Tên Vai trò (Hiển thị):</label>
                    <input type="text" id="customRoleName" name="role_name" required class="w-full" placeholder="Ví dụ: Biên tập viên">
                </div>
                <div class="form-group">
                    <label for="customRoleKey">Khóa Vai trò (Không dấu, không cách, dùng '_'):</label>
                    <input type="text" id="customRoleKey" name="role_key" required class="w-full" placeholder="Ví dụ: bien_tap_vien">
                    <p class="text-xs text-gray-500 mt-1">Khóa này là duy nhất và không thể thay đổi sau khi tạo.</p>
                </div>
                <div class="form-group">
                    <label>Chọn Quyền cho Vai trò Mới:</label>
                    <div class="space-y-6 mt-2">
                        <?php foreach ($pagesByGroup as $groupTitle => $pages): ?>
                        <div class="permission-group border rounded p-4">
                            <h5 class="font-semibold mb-3 cursor-pointer flex justify-between items-center"
                                onclick="togglePermissionGroup(this,'grp-<?= md5($groupTitle) ?>')">
                                <span><?= htmlspecialchars($groupTitle) ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </h5>
                            <div id="grp-<?= md5($groupTitle) ?>" class="permission-group-content overflow-x-auto" style="display:none">
                                <table class="min-w-full table-fixed">
                                    <colgroup>
                                        <col style="width:auto">
                                        <col style="width:80px">
                                        <col style="width:80px">
                                        <col style="width:80px">
                                    </colgroup>
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Quyền</th>
                                            <th class="px-2 py-2 text-center">Không</th>
                                            <th class="px-2 py-2 text-center">Chỉ xem</th>
                                            <th class="px-2 py-2 text-center">Được sửa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pages as $p): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2"><?= htmlspecialchars($p['label']) ?></td>
                                            <?php
                                                $wrap = function($input) {
                                                    return '<td style="padding:0;"><div style="display:flex;justify-content:center;align-items:center;min-height:48px;min-width:80px;">'
                                                           . $input . '</div></td>';
                                                };
                                                $none  = $wrap('<input type="radio" name="mode_'.$p['base'].'" value="none" checked>');
                                                $view  = $p['viewCode']
                                                         ? $wrap('<input data-permission="'.$p['viewCode'].'" type="radio" name="mode_'.$p['base'].'" value="view">')
                                                         : '<td></td>';
                                                $edit  = $p['editCode']
                                                         ? $wrap('<input data-permission="'.$p['editCode'].'" type="radio" name="mode_'.$p['base'].'" value="edit">')
                                                         : '<td></td>';
                                            ?>
                                            <?= $none . $view . $edit ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="PermissionPageEvents.closeModal('createCustomRoleModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo Vai trò</button>
            </div>
        </form>
    </div>
</div>

<?php
include $private_layouts_path . 'admin_footer.php';
?>