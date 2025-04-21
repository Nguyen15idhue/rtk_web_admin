<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\public\pages\profile.php
session_start();
require_once __DIR__ . '/../../private/utils/dashboard_helpers.php'; // Include the helpers
require_once __DIR__ . '/../../private/classes/Database.php'; // Include Database class

// --- Base Path Calculation ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
$project_folder_index = array_search('rtk_web_admin', $script_name_parts);
$base_path_segment = implode('/', array_slice($script_name_parts, 0, $project_folder_index + 1)) . '/';
$base_path = $protocol . $host . $base_path_segment;

// --- Define Includes Path ---
$private_includes_path = __DIR__ . '/../../private/includes/'; // Add this line back

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Use the calculated base_path for redirection
    header('Location: ' . $base_path . 'public/pages/auth/admin_login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
// Use 'admin_name' consistent with user_management.php if available, otherwise fallback
$user_display_name = $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin';

// Fetch admin profile data
$db = new Database();
$conn = $db->getConnection();
$admin_profile = null;

if ($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, name, admin_username, role FROM admin WHERE id = :id");
        $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        $admin_profile = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching admin profile: " . $e->getMessage());
        $admin_profile = null; // Ensure profile is null on error
    } finally {
        $db->close(); // Close connection
    }
} else {
    error_log("Failed to connect to database for admin profile.");
}

// Set default values if profile fetch failed or returned no data
$profile_name = $admin_profile['name'] ?? 'N/A';
$profile_username = $admin_profile['admin_username'] ?? 'N/A'; // Use fetched username
$profile_role = $admin_profile['role'] ?? 'N/A';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Quản trị - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Copied styles from user_management.php */
        :root {
            --primary-500: #3b82f6; --primary-600: #2563eb; --primary-700: #1d4ed8;
            --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb; --gray-300: #d1d5db;
            --gray-400: #9ca3af; --gray-500: #6b7280; --gray-600: #4b5563; --gray-700: #374151;
            --gray-800: #1f2937; --gray-900: #111827;
            --success-500: #10b981; --success-600: #059669; --success-700: #047857;
            --danger-500: #ef4444; --danger-600: #dc2626; --danger-700: #b91c1c;
            --warning-500: #f59e0b; --warning-600: #d97706;
            --info-500: #0ea5e9; --info-600: #0284c7;
            --badge-green-bg: #ecfdf5; --badge-green-text: #065f46;
            --badge-red-bg: #fef2f2; --badge-red-text: #991b1b;
            --badge-yellow-bg: #fffbeb; --badge-yellow-text: #b45309; --badge-yellow-border: #fde68a;
            --rounded-md: 0.375rem; --rounded-lg: 0.5rem; --rounded-full: 9999px;
            --font-size-xs: 0.75rem; --font-size-sm: 0.875rem; --font-size-base: 1rem; --font-size-lg: 1.125rem;
            --font-medium: 500; --font-semibold: 600;
            --border-color: var(--gray-200);
            --transition-speed: 150ms;
        }
        body { font-family: sans-serif; background-color: var(--gray-100); color: var(--gray-800); }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .content-wrapper { flex-grow: 1; padding: 1.5rem; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem 1.5rem; background: white; border-radius: var(--rounded-lg); box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-header h2 { font-size: 1.5rem; font-weight: var(--font-semibold); color: var(--gray-800); }
        .user-info { display: flex; align-items: center; gap: 1rem; font-size: var(--font-size-sm); }
        .user-info span .highlight { color: var(--primary-600); font-weight: var(--font-semibold); }
        .user-info a { color: var(--primary-600); text-decoration: none; }
        .user-info a:hover { text-decoration: underline; }
        .content-section { background: white; border-radius: var(--rounded-lg); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border-color); }
        .content-section h3 { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--gray-700); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border: 1px solid transparent;
            border-radius: var(--rounded-md);
            font-size: var(--font-size-sm);
            font-weight: var(--font-medium);
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: background-color var(--transition-speed) ease-in-out, border-color var(--transition-speed) ease-in-out, color var(--transition-speed) ease-in-out, box-shadow var(--transition-speed) ease-in-out;
            white-space: nowrap;
        }
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn i {
            line-height: 1; /* Ensure icon aligns well */
            margin-right: 0.25rem; /* Add space between icon and text */
        }
        .btn-primary {
            background-color: var(--primary-600);
            color: white;
            border-color: var(--primary-600);
        }
        .btn-primary:hover {
            background-color: var(--primary-700);
            border-color: var(--primary-700);
        }
        .btn-danger {
            background-color: var(--danger-600);
            color: white;
            border-color: var(--danger-600);
        }
        .btn-danger:hover {
            background-color: var(--danger-700);
            border-color: var(--danger-700);
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: var(--font-medium); color: var(--gray-700); font-size: var(--font-size-sm); }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="tel"], .form-group input[type="password"] {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--rounded-md);
            font-size: var(--font-size-sm);
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }
        .form-group input:focus { outline: none; border-color: var(--primary-500); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .form-group input:disabled, .form-group input[readonly] {
            background-color: var(--gray-100);
            cursor: not-allowed;
            opacity: 0.7;
        }
        .form-group .text-xs { font-size: var(--font-size-xs); color: var(--gray-500); margin-top: 0.25rem; }
        .status-message {
            margin-left: 1rem;
            font-size: var(--font-size-sm);
            font-weight: var(--font-medium);
        }
        .status-success { color: var(--success-600); }
        .status-error { color: var(--danger-600); }
        .status-loading { color: var(--info-600); }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } /* Adjust breakpoint if needed */
        .gap-6 { gap: 1.5rem; }
        .lg\:col-span-2 { grid-column: span 2 / span 2; }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(1rem * var(--tw-space-y-reverse)); } /* Simplified spacing */
        .mt-6 { margin-top: 1.5rem; }
        .text-red-500 { color: var(--danger-500); } /* For required asterisk */
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php include $private_includes_path . 'admin_sidebar.php'; ?>

    <main class="content-wrapper">
        <div class="content-header">
            <h2>Hồ sơ Quản trị</h2>
            <div class="user-info">
                <span>Chào mừng, <span class="highlight"><?php echo htmlspecialchars($user_display_name); ?></span>!</span>
                <a href="<?php echo $base_path; ?>public/pages/profile.php">Hồ sơ</a>
                <a href="<?php echo $base_path; ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
            </div>
        </div>

        <div id="admin-profile" class="content-section">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info Card -->
                <div class="lg:col-span-2">
                    <h3>Thông tin cá nhân</h3>
                    <form id="admin-profile-form" onsubmit="updateAdminProfile(event)">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-profile-name">Họ tên <span class="text-red-500">*</span></label>
                                <input type="text" id="admin-profile-name" name="name" required value="<?php echo htmlspecialchars($profile_name); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-email">Email (Tên đăng nhập)</label>
                                <input type="text" id="admin-profile-email" name="admin_username" readonly disabled value="<?php echo htmlspecialchars($profile_username); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin-profile-role">Vai trò</label>
                                <input type="text" id="admin-profile-role" name="role" readonly disabled value="<?php echo htmlspecialchars(ucfirst($profile_role)); // Capitalize first letter ?>">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="save-profile-btn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <span id="profile-status" class="status-message"></span>
                        </div>
                    </form>
                </div>

                <!-- Change Password Card -->
                <div>
                    <h3>Đổi mật khẩu</h3>
                    <form id="admin-password-form" onsubmit="changeAdminPassword(event)">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="admin-current-password">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-current-password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="admin-new-password">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-new-password" name="new_password" required minlength="6">
                                <p class="text-xs">Ít nhất 6 ký tự.</p>
                            </div>
                            <div class="form-group">
                                <label for="admin-confirm-password">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" id="admin-confirm-password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="change-password-btn" class="btn btn-danger">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </button>
                             <span id="password-status" class="status-message"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const profileStatusEl = document.getElementById('profile-status');
    const passwordStatusEl = document.getElementById('password-status');
    const profileForm = document.getElementById('admin-profile-form');
    const passwordForm = document.getElementById('admin-password-form');
    const saveProfileBtn = document.getElementById('save-profile-btn');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const basePath = '<?php echo $base_path; ?>'; // Use PHP defined base path

    function setStatus(element, message, type = 'loading') {
        element.textContent = message;
        element.className = `status-message status-${type}`; // Apply class based on type
    }

    function clearStatus(element) {
        element.textContent = '';
        element.className = 'status-message';
    }

    async function updateAdminProfile(event) {
        event.preventDefault();
        setStatus(profileStatusEl, 'Đang lưu...');
        saveProfileBtn.disabled = true;

        const name = document.getElementById('admin-profile-name').value;

        try {
            // Use basePath in fetch URL
            const response = await fetch(`${basePath}private/actions/setting/process_profile_update.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name }) // Send only name
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                console.error('Non-JSON response:', text);
                setStatus(profileStatusEl, `Lỗi server: Phản hồi không hợp lệ.`, 'error');
                return;
            }

            if (result.success) {
                setStatus(profileStatusEl, 'Cập nhật thành công!', 'success');
                const headerNameSpan = document.querySelector('.user-info .highlight');
                if (headerNameSpan) {
                    headerNameSpan.textContent = name;
                }
            } else {
                setStatus(profileStatusEl, `Lỗi: ${result.message || 'Không thể cập nhật.'}`, 'error');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            setStatus(profileStatusEl, 'Lỗi kết nối hoặc lỗi server.', 'error');
        } finally {
            saveProfileBtn.disabled = false;
            setTimeout(() => { clearStatus(profileStatusEl); }, 5000);
        }
    }

    async function changeAdminPassword(event) {
        event.preventDefault();
        setStatus(passwordStatusEl, 'Đang đổi mật khẩu...');
        changePasswordBtn.disabled = true;

        const currentPassword = document.getElementById('admin-current-password').value;
        const newPassword = document.getElementById('admin-new-password').value;
        const confirmPassword = document.getElementById('admin-confirm-password').value;

        if (newPassword !== confirmPassword) {
            setStatus(passwordStatusEl, 'Lỗi: Mật khẩu mới không khớp.', 'error');
            changePasswordBtn.disabled = false;
            setTimeout(() => { clearStatus(passwordStatusEl); }, 5000);
            return;
        }
        if (newPassword.length < 6) {
            setStatus(passwordStatusEl, 'Lỗi: Mật khẩu mới phải ít nhất 6 ký tự.', 'error');
            changePasswordBtn.disabled = false;
            setTimeout(() => { clearStatus(passwordStatusEl); }, 5000);
            return;
        }

        try {
            const response = await fetch(`${basePath}private/actions/setting/process_password_change.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                })
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                console.error('Non-JSON response:', text);
                setStatus(passwordStatusEl, `Lỗi server: Phản hồi không hợp lệ.`, 'error');
                return;
            }

            if (result.success) {
                setStatus(passwordStatusEl, 'Đổi mật khẩu thành công!', 'success');
                passwordForm.reset();
            } else {
                setStatus(passwordStatusEl, `Lỗi: ${result.message || 'Không thể đổi mật khẩu.'}`, 'error');
            }
        } catch (error) {
            console.error('Error changing password:', error);
            setStatus(passwordStatusEl, 'Lỗi kết nối hoặc lỗi server.', 'error');
        } finally {
            changePasswordBtn.disabled = false;
            setTimeout(() => { clearStatus(passwordStatusEl); }, 5000);
        }
    }
</script>

</body>
</html>
