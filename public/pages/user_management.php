<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\user_management.php
session_start();
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

// --- Includes and Setup ---
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

// --- Base Path Calculation ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
$project_folder_index = array_search('rtk_web_admin', $script_name_parts);
// Ensure base path ends with a slash if the project folder is found
if ($project_folder_index !== false) {
    $base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
} else {
    // Fallback if 'rtk_web_admin' is not in the path (e.g., running from root)
    // This might need adjustment based on your server setup
    $base_path_segment = '/';
}
$base_path = $protocol . $host . $base_path_segment;

// --- Include Required Files ---
require_once __DIR__ . '/../../private/utils/functions.php'; // General helpers (includes format_date)
require_once __DIR__ . '/../../private/utils/user_helpers.php'; // User-specific helpers (includes get_user_status_display)
require_once __DIR__ . '/../../private/actions/user/fetch_users.php'; // User fetching logic

// Add current admin role for permission checks
$admin_role = $_SESSION['admin_role'] ?? '';

$user_display_name = $_SESSION['admin_username'] ?? 'Admin';

// --- Get Filters ---
$filters = [
    'search' => filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
    'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];
// Debug PHP error log
error_log('[DEBUG] Search keyword: ' . $filters['search']);
// Debug JavaScript console
echo '<script>console.log("DEBUG Search keyword:", ' . json_encode($filters['search'], JSON_UNESCAPED_UNICODE) . ');</script>';

// --- Pagination Setup ---
$items_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// --- Fetch Users ---
$userData = fetch_paginated_users($filters, $current_page, $items_per_page);
$users = $userData['users'];
$total_items = $userData['total_count'];
$total_pages = $userData['total_pages'];
$current_page = $userData['current_page']; // Use the validated page number from the function

// --- Build Pagination URL ---
$pagination_params = $filters; // Start with existing filters
unset($pagination_params['page']);
// Ensure the base URL for pagination doesn't include existing query string if filters are empty
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
if (!empty($pagination_params)) {
    $pagination_base_url .= '?' . http_build_query($pagination_params);
    $pagination_base_url .= '&'; // Add separator for the page param
} else {
    $pagination_base_url .= '?'; // Start query string for the page param
}
// Remove trailing '&' if it exists
$pagination_base_url = rtrim($pagination_base_url, '&');
// Ensure there's a '?' before adding 'page=' if no other params exist
if (strpos($pagination_base_url, '?') === false) {
    $pagination_base_url .= '?';
} else if (substr($pagination_base_url, -1) !== '?') {
    $pagination_base_url .= '&';
}

// --- Page Setup for Header/Sidebar ---
$page_title = 'Quản lý Người dùng';
$private_includes_path = __DIR__ . '/../../private/includes/';

include $private_includes_path . 'admin_header.php';
?>
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/tables/tables-badges.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/forms.css">
<link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/layouts/header.css">

<!-- Page Specific Styles -->
<style>
.content-wrapper .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
.content-wrapper .modal-content { background-color: #fefefe; margin: 10% auto; padding: 25px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: var(--rounded-lg); position: relative; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19); }
.content-wrapper .modal-header { padding-bottom: 15px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
.content-wrapper .modal-header h4 { margin: 0; font-size: 1.25rem; font-weight: var(--font-semibold); color: var(--gray-800); }
.content-wrapper .modal-close { color: var(--gray-400); font-size: 1.5rem; font-weight: bold; cursor: pointer; border: none; background: none; line-height: 1; padding: 0.5rem; border-radius: var(--rounded-full); transition: color var(--transition-speed) ease-in-out, background-color var(--transition-speed) ease-in-out; }
.content-wrapper .modal-close:hover, .content-wrapper .modal-close:focus { color: var(--gray-700); background-color: var(--gray-100); text-decoration: none; outline: none; }
.content-wrapper .modal-body { margin-bottom: 20px; }
.content-wrapper .modal-body .detail-row { display: flex; margin-bottom: 10px; font-size: var(--font-size-sm); }
.content-wrapper .modal-body .detail-label { font-weight: var(--font-semibold); color: var(--gray-600); width: 150px; flex-shrink: 0; }
.content-wrapper .modal-body .detail-value { color: var(--gray-800); }
.content-wrapper .modal-footer { padding-top: 15px; border-top: 1px solid var(--border-color); text-align: right; }
.content-wrapper .modal-footer .btn { margin-left: 0.5rem; }
.content-wrapper #createUserForm .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
.content-wrapper #createUserForm .error-message {
    color: var(--danger-600);
    font-size: var(--font-size-sm);
    margin-top: 1rem;
    text-align: left;
}
/* button styles moved to components/buttons.css */
</style>

<?php
include $private_includes_path . 'admin_sidebar.php';
?>

<!-- Main Content Wrapper -->
<main class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <h2><?php echo $page_title; ?></h2>
        <div class="user-info">
            <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
            <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
            <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div id="admin-user-management" class="content-section">
        <div class="header-actions">
             <h3>Quản lý người dùng (KH)</h3>
            <?php if ($admin_role !== 'customercare'): ?>
                <button class="btn btn-primary" onclick="openCreateUserModal()" data-permission="user_create">
                    <i class="fas fa-plus"></i> Thêm người dùng
                </button>
            <?php endif; ?>
        </div>
         <p class="text-xs sm:text-sm text-gray-600 mb-4" style="font-size: var(--font-size-sm); color: var(--gray-600); margin-bottom: 1rem;">Quản lý tài khoản người dùng đăng ký (không phải tài khoản quản trị).</p>

        <form method="GET" action="">
            <div class="filter-bar">
                <input type="search" placeholder="Tìm Email, Tên..." name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo ($filters['status'] == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="inactive" <?php echo ($filters['status'] == 'inactive') ? 'selected' : ''; ?>>Vô hiệu hóa</option>
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary" style="text-decoration: none;"><i class="fas fa-times"></i> Xóa lọc</a>
            </div>
        </form>

        <div class="transactions-table-wrapper">
            <table class="transactions-table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Loại TK</th>
                        <th>Tên công ty</th>
                        <th>Mã số thuế</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th class="actions" style="text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td><?php echo $user['is_company'] ? 'Công ty' : 'Cá nhân'; ?></td>
                                <td><?php echo $user['is_company'] ? htmlspecialchars($user['company_name'] ?? '-') : '-'; ?></td>
                                <td><?php echo $user['is_company'] ? htmlspecialchars($user['tax_code'] ?? '-') : '-'; ?></td>
                                <td><?php echo format_date($user['created_at']); ?></td>
                                <td><?php echo get_user_status_display($user); ?></td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-view" title="Xem chi tiết" onclick="viewUserDetails('<?php echo htmlspecialchars($user['id']); ?>')"><i class="fas fa-eye"></i></button>
                                        <?php if ($admin_role !== 'customercare'): ?>
                                            <button class="btn-icon btn-edit" title="Sửa" onclick="openEditUserModal('<?php echo htmlspecialchars($user['id']); ?>')" data-permission="user_edit"><i class="fas fa-pencil-alt"></i></button>
                                            <?php
                                                $is_inactive = isset($user['deleted_at']) && $user['deleted_at'] !== null && $user['deleted_at'] !== '';
                                                $action = $is_inactive ? 'enable' : 'disable';
                                                $icon = $is_inactive ? 'fa-toggle-on' : 'fa-toggle-off';
                                                $title = $is_inactive ? 'Kích hoạt' : 'Vô hiệu hóa';
                                                $btn_class = $is_inactive ? 'btn-success' : 'btn-secondary';
                                            ?>
                                            <button class="btn-icon <?php echo $btn_class; ?>" onclick="toggleUserStatus('<?php echo htmlspecialchars($user['id']); ?>', '<?php echo $action; ?>')" title="<?php echo $title; ?>">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="no-results-row">
                            <td colspan="10">Không tìm thấy người dùng phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-footer">
             <div class="pagination-info">
                <?php if ($total_items > 0):
                    $start_item = ($current_page - 1) * $items_per_page + 1;
                    $end_item = min($start_item + $items_per_page - 1, $total_items);
                ?>
                    Hiển thị <?php echo $start_item; ?>-<?php echo $end_item; ?> của <?php echo $total_items; ?> người dùng
                <?php else: ?>
                    Không có người dùng nào
                <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
            <div class="pagination-controls">
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page - 1); ?>'" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Tr</button>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <button class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . $i; ?>'"><?php echo $i; ?></button>
                <?php endfor; ?>
                <button onclick="window.location.href='<?php echo $pagination_base_url . 'page=' . ($current_page + 1); ?>'" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Sau</button>
            </div>
             <?php endif; ?>
        </div>
    </div> <!-- End content-section -->

    <!-- Modals remain inside the main wrapper but outside the primary content section -->
    <!-- View User Modal -->
    <div id="viewUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Chi tiết Người dùng</h4>
                <span class="modal-close" onclick="closeModal('viewUserModal')">&times;</span>
            </div>
            <div class="modal-body" id="viewUserDetailsBody">
                <p>Đang tải...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('viewUserModal')">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
             <form id="editUserForm">
                <div class="modal-header">
                    <h4>Chỉnh sửa Người dùng</h4>
                    <span class="modal-close" onclick="closeModal('editUserModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="form-group">
                        <label for="editUsername">Tên đăng nhập</label>
                        <input type="text" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Số điện thoại</label>
                        <input type="tel" id="editPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="editIsCompany" name="is_company" onchange="toggleCompanyFields('edit')">
                            <label for="editIsCompany">Là tài khoản công ty?</label>
                        </div>
                    </div>
                    <div class="company-fields" id="editCompanyFields">
                        <div class="form-group">
                            <label for="editCompanyName">Tên công ty</label>
                            <input type="text" id="editCompanyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="editTaxCode">Mã số thuế</label>
                            <input type="text" id="editTaxCode" name="tax_code">
                        </div>
                    </div>
                     <div id="editUserError" class="error-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('createUserModal')">&times;</span>
            <h2>Thêm người dùng mới</h2>
            <form id="createUserForm">
                 <div class="modal-body"> <!-- Wrap form fields in modal-body -->
                    <div class="form-group">
                        <label for="createUsername">Tên người dùng:</label>
                        <input type="text" id="createUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="createEmail">Email:</label>
                        <input type="email" id="createEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="createPassword">Mật khẩu:</label>
                        <input type="password" id="createPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="createPhone">Số điện thoại:</label>
                        <input type="tel" id="createPhone" name="phone">
                    </div>
                     <div class="form-group form-check">
                        <input type="checkbox" id="createIsCompany" name="is_company" onchange="toggleCompanyFields('create')">
                        <label for="createIsCompany">Là công ty?</label>
                    </div>
                    <div id="createCompanyFields" class="company-fields">
                        <div class="form-group">
                            <label for="createCompanyName">Tên công ty:</label>
                            <input type="text" id="createCompanyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="createTaxCode">Mã số thuế:</label>
                            <input type="text" id="createTaxCode" name="tax_code">
                        </div>
                    </div>
                     <p id="createUserError" class="error-message"></p>
                 </div> <!-- End modal-body -->
                <div class="form-actions modal-footer"> <!-- Use modal-footer for consistency -->
                     <button type="button" class="btn btn-secondary" onclick="closeModal('createUserModal')">Hủy</button>
                     <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>

</main> <!-- End content-wrapper -->

<!-- Page Specific Scripts -->
<script>
    const viewModal = document.getElementById('viewUserModal');
    const editModal = document.getElementById('editUserModal');
    const createModal = document.getElementById('createUserModal');
    const viewDetailsBody = document.getElementById('viewUserDetailsBody');
    const editUserForm = document.getElementById('editUserForm');
    const createUserForm = document.getElementById('createUserForm');
    const editCompanyFields = document.getElementById('editCompanyFields');
    const createCompanyFields = document.getElementById('createCompanyFields');
    const editUserError = document.getElementById('editUserError');
    const createUserError = document.getElementById('createUserError');
    const basePath = '<?php echo rtrim($base_path, '/'); ?>'; // Ensure no trailing slash for JS fetch

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
        // Reset forms and errors when closing
        if (modalId === 'editUserModal') {
            editUserForm.reset();
            editCompanyFields.classList.remove('visible');
            editUserError.textContent = ''; // Clear previous errors
            // Ensure required attributes are reset if needed
             document.getElementById('editCompanyName').required = false;
             document.getElementById('editTaxCode').required = false;
        } else if (modalId === 'createUserModal') {
            createUserForm.reset();
            createCompanyFields.classList.remove('visible');
            createUserError.textContent = ''; // Clear previous errors
             // Ensure required attributes are reset if needed
             document.getElementById('createCompanyName').required = false;
             document.getElementById('createTaxCode').required = false;
        }
    }

    function toggleCompanyFields(formType) {
        const isCompanyCheckbox = document.getElementById(`${formType}IsCompany`);
        const companyFieldsDiv = document.getElementById(`${formType}CompanyFields`);
        const companyNameInput = document.getElementById(`${formType}CompanyName`);
        const taxCodeInput = document.getElementById(`${formType}TaxCode`);

        if (isCompanyCheckbox.checked) {
            companyFieldsDiv.classList.add('visible');
            companyNameInput.required = true;
            taxCodeInput.required = true;
        } else {
            companyFieldsDiv.classList.remove('visible');
            companyNameInput.required = false;
            taxCodeInput.required = false;
            // Optionally clear the fields when unchecked
            // companyNameInput.value = '';
            // taxCodeInput.value = '';
        }
    }

    // Close modal if clicking outside of it
    window.onclick = function(event) {
        if (event.target == viewModal) {
            closeModal('viewUserModal');
        }
        if (event.target == editModal) {
            closeModal('editUserModal');
        }
        if (event.target == createModal) {
             closeModal('createUserModal');
        }
    }

    function openCreateUserModal() {
        // Reset form before showing
        createUserForm.reset();
        createCompanyFields.classList.remove('visible');
        createUserError.textContent = '';
        document.getElementById('createCompanyName').required = false; // Reset required status
        document.getElementById('createTaxCode').required = false;    // Reset required status
        createModal.style.display = 'block';
    }

    createUserForm.addEventListener('submit', function(event) {
        event.preventDefault();
        createUserError.textContent = ''; // Clear previous errors
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang thêm...';

        const formData = new FormData(createUserForm);
        // Convert FormData to plain object for JSON stringify
        const data = {};
        formData.forEach((value, key) => {
            // Handle checkbox value explicitly
            if (key === 'is_company') {
                data[key] = 1; // Set to 1 if checked
            } else {
                data[key] = value;
            }
        });
        // If checkbox was not checked, 'is_company' won't be in formData, so set default
        if (!formData.has('is_company')) {
            data['is_company'] = 0;
        }


        fetch(`${basePath}/private/actions/setting/process_user_create.php`, { // Use basePath variable
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
        })
        .then(response => {
            // ... (rest of the fetch logic remains the same) ...
             return response.text().then(text => {
                const ct = response.headers.get("content-type") || "";
                if (response.ok && ct.includes("application/json")) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Phản hồi JSON không hợp lệ từ máy chủ: ' + text);
                    }
                } else {
                    let errorMsg = `Lỗi HTTP ${response.status}: ${response.statusText}`;
                    if (text) {
                        // Try to parse potential JSON error message from server
                        try {
                            const errorJson = JSON.parse(text);
                            if (errorJson && errorJson.message) {
                                errorMsg += ` - ${errorJson.message}`;
                            } else {
                                errorMsg += ` - Phản hồi từ máy chủ: ${text.substring(0, 200)}${text.length > 200 ? '...' : ''}`;
                            }
                        } catch(e) {
                             errorMsg += ` - Phản hồi từ máy chủ: ${text.substring(0, 200)}${text.length > 200 ? '...' : ''}`;
                        }
                    }
                    throw new Error(errorMsg);
                }
            });
        })
        .then(result => {
            if (result.success) {
                closeModal('createUserModal');
                alert('Thêm người dùng thành công!');
                location.reload(); // Reload to see the new user
            } else {
                // Display specific error message from server if available
                createUserError.textContent = 'Lỗi: ' + (result.message || 'Không thể thêm người dùng. Vui lòng kiểm tra lại thông tin.');
            }
        })
        .catch(err => {
            console.error('Error creating user:', err);
            // Display more user-friendly error
            createUserError.textContent = 'Đã xảy ra lỗi khi gửi yêu cầu: ' + err.message;
        })
        .finally(() => {
            // Re-enable button and restore text
            submitButton.disabled = false;
            submitButton.textContent = 'Thêm người dùng';
        });
    });

    function viewUserDetails(userId) {
        viewDetailsBody.innerHTML = '<p>Đang tải...</p>';
        viewModal.style.display = 'block';

        fetch(`${basePath}/private/actions/setting/fetch_user_details.php?user_id=${userId}`) // Use basePath
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success && result.data) {
                    const user = result.data;
                    let detailsHtml = `
                        <div class="detail-row"><span class="detail-label">ID:</span> <span class="detail-value">${user.id}</span></div>
                        <div class="detail-row"><span class="detail-label">Tên đăng nhập:</span> <span class="detail-value">${user.username || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value">${user.email}</span></div>
                        <div class="detail-row"><span class="detail-label">Số điện thoại:</span> <span class="detail-value">${user.phone || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Loại tài khoản:</span> <span class="detail-value">${user.account_type_text}</span></div>
                    `;
                    if (user.is_company == 1) { // Check against 1
                        detailsHtml += `
                            <div class="detail-row"><span class="detail-label">Tên công ty:</span> <span class="detail-value">${user.company_name || '-'}</span></div>
                            <div class="detail-row"><span class="detail-label">Mã số thuế:</span> <span class="detail-value">${user.tax_code || '-'}</span></div>
                        `;
                    }
                    detailsHtml += `
                        <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${user.created_at_formatted}</span></div>
                        <div class="detail-row"><span class="detail-label">Cập nhật lần cuối:</span> <span class="detail-value">${user.updated_at_formatted || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${user.status_text} ${user.deleted_at_formatted ? '(' + user.deleted_at_formatted + ')' : ''}</span></div>
                    `;
                    viewDetailsBody.innerHTML = detailsHtml;
                } else {
                    viewDetailsBody.innerHTML = `<p style="color: red;">Lỗi: ${result.message || 'Không thể tải thông tin người dùng.'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
                viewDetailsBody.innerHTML = `<p style="color: red;">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}</p>`;
            });
    }

    function openEditUserModal(userId) {
        // Reset form before fetching new data
        editUserForm.reset();
        editCompanyFields.classList.remove('visible');
        editUserError.textContent = '';
        document.getElementById('editCompanyName').required = false; // Reset required status
        document.getElementById('editTaxCode').required = false;    // Reset required status
        editModal.style.display = 'block';

        fetch(`${basePath}/private/actions/setting/fetch_user_details.php?user_id=${userId}`) // Use basePath
            .then(response => response.ok ? response.json() : Promise.reject(`HTTP error! status: ${response.status}`))
            .then(result => {
                if (result.success && result.data) {
                    const user = result.data;
                    document.getElementById('editUserId').value = user.id;
                    document.getElementById('editUsername').value = user.username || '';
                    document.getElementById('editEmail').value = user.email || '';
                    document.getElementById('editPhone').value = user.phone || '';
                    // Set checkbox state based on numeric value
                    document.getElementById('editIsCompany').checked = (user.is_company == 1);
                    // Trigger change handler to show/hide fields and set required attributes
                    toggleCompanyFields('edit');
                    // Populate company fields if applicable
                    if (user.is_company == 1) {
                        document.getElementById('editCompanyName').value = user.company_name || '';
                        document.getElementById('editTaxCode').value = user.tax_code || '';
                    }
                } else {
                    // Display error inside the modal for better UX
                    editUserError.textContent = `Lỗi tải dữ liệu: ${result.message || 'Không thể lấy thông tin người dùng.'}`;
                    // Optionally disable form fields or show a more prominent error
                }
            })
            .catch(error => {
                console.error('Error fetching user details for edit:', error);
                 // Display error inside the modal
                editUserError.textContent = `Đã xảy ra lỗi khi tải dữ liệu: ${error}`;
            });
    }

    editUserForm.addEventListener('submit', function(event) {
        event.preventDefault();
        editUserError.textContent = ''; // Clear previous errors
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang lưu...';

        // Ensure is_company is sent even if unchecked
        if (!formData.has('is_company')) {
            formData.set('is_company', '0');
        } else {
             formData.set('is_company', '1'); // Ensure value is 1 if checked
        }

        fetch(`${basePath}/private/actions/setting/process_user_update.php`, { // Use basePath
            method: 'POST',
            body: formData // FormData handles content type automatically
        })
        .then(response => {
             // Check content type before parsing JSON
             const contentType = response.headers.get("content-type");
             if (contentType && contentType.indexOf("application/json") !== -1) {
                 return response.json();
             } else {
                 return response.text().then(text => {
                     throw new Error("Phản hồi không phải JSON: " + text);
                 });
             }
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'Cập nhật thành công!');
                closeModal('editUserModal');
                window.location.reload(); // Reload to see changes
            } else {
                // Display specific error message from server if available
                editUserError.textContent = 'Lỗi: ' + (data.message || 'Không thể cập nhật người dùng. Vui lòng kiểm tra lại thông tin.');
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
             // Display error inside the modal
            editUserError.textContent = 'Đã xảy ra lỗi khi gửi yêu cầu: ' + error.message;
        })
        .finally(() => {
             // Re-enable button and restore text
             submitButton.disabled = false;
             submitButton.textContent = 'Lưu thay đổi';
        });
    });

    function toggleUserStatus(userId, action) {
        const actionText = action === 'disable' ? 'vô hiệu hóa' : 'kích hoạt';
        if (confirm(`Bạn có chắc muốn ${actionText} người dùng ID ${userId} không?`)) {
            fetch(`${basePath}/private/actions/setting/process_user_toggle_status.php`, { // Use basePath
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', // Keep as form data
                    'Accept': 'application/json'
                },
                body: `user_id=${encodeURIComponent(userId)}&action=${encodeURIComponent(action)}`
            })
            .then(response => {
                // ... (rest of the fetch logic remains the same) ...
                const ct = response.headers.get("content-type") || "";
                return response.text().then(text => {
                    const firstChar = text.trim()[0];
                    if (response.ok && ct.includes("application/json") &&
                        (firstChar === "{" || firstChar === "[")) {
                       try {
                           return JSON.parse(text);
                       } catch (e) {
                            throw new Error("Phản hồi JSON không hợp lệ: " + text);
                       }
                    }
                    // Attempt to parse JSON even on error for potential error messages
                    if (ct.includes("application/json")) {
                         try {
                            const errorJson = JSON.parse(text);
                            if (errorJson && errorJson.message) {
                                throw new Error(`Lỗi ${response.status}: ${errorJson.message}`);
                            }
                         } catch(e) { /* Ignore parsing error if not JSON */ }
                    }
                    // Fallback error message
                    let msg = `Lỗi HTTP ${response.status}: ${response.statusText}`;
                    msg += ` – Server response:\n${text.substr(0,200)}`;
                    throw new Error(msg);
                });
            })
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Thao tác thành công!');
                    window.location.reload(); // Reload to reflect status change
                } else {
                    // Show specific error from server response
                    alert('Lỗi: ' + (data.message || 'Không thể thay đổi trạng thái người dùng.'));
                }
            })
            .catch(error => {
                console.error('Error toggling user status:', error);
                // Provide more context in the alert
                alert('Đã xảy ra lỗi khi thực hiện thao tác: ' + error.message + '. Vui lòng thử lại hoặc kiểm tra console.');
            });
        }
    }
</script>

<?php
// Include Footer
include $private_includes_path . 'admin_footer.php';
?>