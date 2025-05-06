const profileStatusEl = document.getElementById('profile-status');
const passwordStatusEl = document.getElementById('password-status');
const profileForm = document.getElementById('admin-profile-form');
const passwordForm = document.getElementById('admin-password-form');
const saveProfileBtn = document.getElementById('save-profile-btn');
const changePasswordBtn = document.getElementById('change-password-btn');

function setStatus(element, message, type = 'loading') {
    element.textContent = message;
    element.className = `status-message status-${type}`;
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
        const res = await api.postJson(`${basePath}public/actions/setting/index.php?action=process_profile_update`, { name });
        if (!res.success) throw new Error(res.message || 'Không thể cập nhật.');
        setStatus(profileStatusEl, 'Cập nhật thành công!', 'success');
        const headerNameSpan = document.querySelector('.user-info .highlight');
        if (headerNameSpan) headerNameSpan.textContent = name;
    } catch (err) {
        setStatus(profileStatusEl, `Lỗi: ${err.message}`, 'error');
    } finally {
        saveProfileBtn.disabled = false;
        setTimeout(() => clearStatus(profileStatusEl), 5000);
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
        setTimeout(() => clearStatus(passwordStatusEl), 5000);
        return;
    }
    if (newPassword.length < 6) {
        setStatus(passwordStatusEl, 'Lỗi: Mật khẩu mới phải ít nhất 6 ký tự.', 'error');
        changePasswordBtn.disabled = false;
        setTimeout(() => clearStatus(passwordStatusEl), 5000);
        return;
    }

    try {
        const res = await api.postJson(`${basePath}public/actions/setting/index.php?action=process_password_change`, {
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        });
        if (!res.success) throw new Error(res.message || 'Không thể đổi mật khẩu.');
        setStatus(passwordStatusEl, 'Đổi mật khẩu thành công!', 'success');
        passwordForm.reset();
    } catch (err) {
        setStatus(passwordStatusEl, `Lỗi: ${err.message}`, 'error');
    } finally {
        changePasswordBtn.disabled = false;
        setTimeout(() => clearStatus(passwordStatusEl), 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    (async () => {
        try {
            const res = await api.getJson(`${basePath}public/actions/setting/index.php?action=process_profile_fetch`);
            if (res.success && res.data) {
                const d = res.data;
                document.getElementById('admin-profile-name').value = d.name || '';
                document.getElementById('admin-profile-email').value = d.admin_username || '';
                document.getElementById('admin-profile-role').value = d.role.charAt(0).toUpperCase() + d.role.slice(1);
            } else {
                throw new Error(res.message || 'Không thể tải profile.');
            }
        } catch (err) {
            window.showToast(`Lỗi tải profile: ${err.message}`, 'error');
        }
    })();

    profileForm.addEventListener('submit', updateAdminProfile);
    passwordForm.addEventListener('submit', changeAdminPassword);
});
