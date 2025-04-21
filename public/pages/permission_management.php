<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\permission_management.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php';
$private_includes_path = __DIR__ . '/../../private/includes/';
$user_display_name = $_SESSION['admin_username'] ?? 'Admin';
$base_path = '/'; // Adjust if necessary

// Check if current user is SuperAdmin for editing
$is_super_admin = (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin');

?>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2 class="text-2xl font-semibold">Quản lý Phân quyền</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
            <?php if ($is_super_admin): ?>
                <button class="btn btn-primary ml-4" onclick="openCreateRoleModal()">
                    <i class="fas fa-user-shield mr-1"></i> Thêm QTV/Vận hành
                </button>
            <?php endif; ?>
        </div>

        <div id="admin-permission-management" class="content-section">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3 md:gap-2">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Quản lý phân quyền</h2>
                <button class="btn-primary self-start md:self-auto w-full md:w-auto" onclick="openCreateRoleModal()" data-permission="admin_user_create">
                    <i class="fas fa-user-plus mr-1"></i> Thêm QTV/Vận hành
                </button>
            </div>
            <p class="text-xs sm:text-sm text-gray-600 mb-6">Quản lý vai trò và quyền hạn truy cập cho tài khoản quản trị hệ thống.</p>

            <!-- Permission Cards: Responsive Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
                <!-- Role Card Mẫu (Super Admin) -->
                <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-red-300">
                    <h3 class="text-base md:text-lg font-semibold text-red-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-crown text-yellow-500"></i> Super Admin
                    </h3>
                    <p class="text-xs text-gray-600 mb-3">Toàn quyền truy cập.</p>
                    <ul class="text-xs space-y-1 list-disc list-inside text-gray-700 mb-3">
                        <li>QL Người dùng (KH)</li>
                        <li>QL TK Đo đạc</li>
                        <!-- ... các quyền khác ... -->
                        <li>QL Phân quyền</li>
                    </ul>
                    <button class="btn-secondary mt-2 text-xs" disabled>Không thể sửa</button>
                </div>

                <!-- Role: Quản trị viên (Admin) -->
                <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-blue-300">
                    <h3 class="text-base md:text-lg font-semibold text-blue-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> Quản trị viên
                    </h3>
                    <p class="text-xs text-gray-600 mb-3">Quản lý hoạt động hàng ngày.</p>
                    <form>
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
                                <input type="checkbox" class="mr-2 h-3 w-3" disabled data-role="Admin" data-permission="permission_management"> QL Phân quyền
                            </label>
                            <!-- ... các quyền khác ... -->
                        </div>
                        <button type="button" class="btn-primary mt-2 text-xs" onclick="savePermissions('Admin', event)" data-permission="permission_edit">
                            Lưu quyền Admin
                        </button>
                    </form>
                </div>

                <!-- Role: Vận hành (Operator) -->
                <div class="bg-white p-4 sm:p-5 rounded-lg shadow border border-green-300">
                    <h3 class="text-base md:text-lg font-semibold text-green-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-user-cog"></i> Vận hành
                    </h3>
                    <p class="text-xs text-gray-600 mb-3">Xem thông tin, hỗ trợ cơ bản.</p>
                    <form>
                        <div class="space-y-2 text-xs max-h-40 sm:max-h-48 overflow-y-auto pr-2 border-t border-b py-2 my-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked disabled> Dashboard (Xem)
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Operator" data-permission="user_management"> QL User (Xem)
                            </label>
                            <!-- ... các quyền khác ... -->
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3 accent-primary-600" checked data-role="Operator" data-permission="reports"> Xem Báo cáo
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2 h-3 w-3" disabled data-role="Operator" data-permission="permission_management"> QL Phân quyền
                            </label>
                        </div>
                        <button type="button" class="btn-primary mt-2 text-xs" onclick="savePermissions('Operator', event)" data-permission="permission_edit">
                            Lưu quyền Vận hành
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-xs text-red-600 mt-4 italic">*Lưu ý: Chỉ Super Admin có thể thay đổi quyền. TK mới tạo cần đổi MK mặc định.</p>
        </div>
    </main>
</div>

<style>
   .modal {
     display: none;
     position: fixed;
     top: 0; left: 0;
     width: 100%; height: 100%;
     background: rgba(0,0,0,0.5);
     align-items: center;
     justify-content: center;
     z-index: 999;
   }
   .modal-content {
     background: #fff;
     padding: 1rem;
     border-radius: 0.5rem;
     max-width: 500px;
     width: 90%;
   }
</style>

<script>
    const basePath = '<?php echo $base_path; ?>';

    // Load current permissions for each role on page load
    ['Admin','Operator'].forEach(role => {
        fetch(`${basePath}private/actions/auth/fetch_permissions.php?role=${role.toLowerCase()}`)
            .then(res => res.json())
            .then(result => {
                if (result.success && Array.isArray(result.data)) {
                    result.data.forEach(item => {
                        const selector = `input[type="checkbox"][data-role="${role}"][data-permission="${item.permission}"]`;
                        const cb = document.querySelector(selector);
                        if (cb) cb.checked = item.allowed === '1' || item.allowed === 1;
                    });
                }
            })
            .catch(err => console.error(`Error fetching perms for ${role}:`, err));
    });

    function savePermissions(role, event) {
        event.preventDefault();
        if (!<?php echo json_encode($is_super_admin); ?>) {
            alert('Bạn không có quyền thực hiện hành động này.');
            return;
        }
        const permissions = {};
        document.querySelectorAll(`input[type="checkbox"][data-role="${role}"]`).forEach(cb => {
            permissions[cb.dataset.permission] = cb.checked;
        });
        fetch(`${basePath}private/actions/auth/process_permissions_update.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role: role.toLowerCase(), permissions })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Cập nhật quyền thành công!');
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể cập nhật quyền.'));
            }
        })
        .catch(err => {
            console.error('Error updating perms:', err);
            alert('Đã xảy ra lỗi.');
        });
    }

    // Disable checkboxes if not SuperAdmin on page load
    if (!<?php echo json_encode($is_super_admin); ?>) {
        document.querySelectorAll('#admin-permission-management input[type="checkbox"]:not([data-fixed-disabled])').forEach(cb => {
            cb.disabled = true;
            cb.style.cursor = 'not-allowed';
            const label = document.querySelector(`label[for="${cb.id}"]`);
            if (label) {
                label.style.cursor = 'not-allowed';
                label.style.color = '#6b7280'; // Dim the label
            }
        });
    }

    function openCreateRoleModal() {
        document.getElementById('createRoleForm').reset();
        document.getElementById('createRoleModal').style.display = 'block';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal && (modal.style.display = 'none');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('createRoleModal');
        if (event.target === modal) closeModal('createRoleModal');
    };

    // Handle form submission
    window.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('createRoleForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                const data = {
                    name: this.name.value.trim(),
                    username: this.username.value.trim(),
                    password: this.password.value,
                    role: this.role.value
                };

                fetch(`${basePath}private/actions/auth/process_admin_create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message || 'Tạo tài khoản thành công!');
                        closeModal('createRoleModal');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + (result.message || 'Không thể tạo tài khoản.'));
                    }
                })
                .catch(err => {
                    console.error('Error creating admin:', err);
                    alert('Đã xảy ra lỗi. Vui lòng thử lại.');
                })
                .finally(() => submitBtn.disabled = false);
            });
        }
    });
</script>

<!-- Create Admin/Operator Modal -->
<div id="createRoleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Thêm QTV/Vận hành</h4>
            <span class="modal-close" onclick="closeModal('createRoleModal')">&times;</span>
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
                        <option value="operator">Vận hành</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createRoleModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </div>
        </form>
    </div>
</div>
