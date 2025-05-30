document.addEventListener('DOMContentLoaded', ()=> {
    const basePath = ('/' + (window.appConfig?.basePath || ''))
                       .replace(/\/+/g,'/')
                       .replace(/\/?$/,'/') ;

    const helpers = window.helpers;
    const { closeModal, openModal } = helpers;
    const { getJson, postJson, postForm } = window.api;

    const apiBasePath = `${basePath}public/handlers/user/index.php`;

    const canEditUsers = window.appConfig?.permissions?.user_management_edit || false;

    // Generic Modal Elements
    const genericModalTitle = document.getElementById('genericModalTitle');
    const genericModalBody = document.getElementById('genericModalBody');
    const genericModalPrimaryButton = document.getElementById('genericModalPrimaryButton');

    function getCreateUserFormHTML() {
        return `
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username">Tên người dùng:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group" id="passwordGroup">
                        <label for="password">Mật khẩu:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại:</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" id="isCompany" name="is_company">
                        <label for="isCompany">Là công ty?</label>
                    </div>
                    <div id="companyFields" class="company-fields" style="display: none;">
                        <div class="form-group">
                            <label for="companyName">Tên công ty:</label>
                            <input type="text" id="companyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="taxCode">Mã số thuế:</label>
                            <input type="text" id="taxCode" name="tax_code">
                        </div>
                        <div class="form-group">
                            <label for="companyAddress">Địa chỉ công ty:</label>
                            <input type="text" id="companyAddress" name="company_address">
                        </div>
                    </div>
                    <p id="userFormError" class="error-message"></p>
                </div>
            </form>
        `;
    }

    function getEditUserFormHTML() {
        return `
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="isCompany" name="is_company">
                            <label for="isCompany">Là tài khoản công ty?</label>
                        </div>
                    </div>
                    <div class="company-fields" id="companyFields" style="display: none;">
                        <div class="form-group">
                            <label for="companyName">Tên công ty</label>
                            <input type="text" id="companyName" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="taxCode">Mã số thuế</label>
                            <input type="text" id="taxCode" name="tax_code">
                        </div>
                        <div class="form-group">
                            <label for="companyAddress">Địa chỉ công ty:</label>
                            <input type="text" id="companyAddress" name="company_address">
                        </div>
                    </div>
                    <p id="userFormError" class="error-message"></p>
                </div>
            </form>
        `;
    }

    // Đổi mật khẩu form
    function getChangePasswordFormHTML() {
        return `
            <form id="passwordForm">
                <input type="hidden" id="userIdPwd" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="password">Mật khẩu mới:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Xác nhận mật khẩu:</label>
                        <input type="password" id="confirmPassword" name="confirm_password" required>
                    </div>
                    <p id="passwordFormError" class="error-message"></p>
                </div>
            </form>
        `;
    }

    function setupUserFormEventListeners(formType) {
        const userForm = document.getElementById('userForm');
        if (!userForm) return;

        userForm.addEventListener('submit', handleUserFormSubmit);

        const isCompanyCheckbox = document.getElementById('isCompany');
        const companyFieldsDiv = document.getElementById('companyFields');
        const companyNameInput = document.getElementById('companyName');
        const taxCodeInput = document.getElementById('taxCode');
        const companyAddressInput = document.getElementById('companyAddress');

        if (isCompanyCheckbox && companyFieldsDiv) {
            isCompanyCheckbox.addEventListener('change', () => {
                const isChecked = isCompanyCheckbox.checked;
                companyFieldsDiv.style.display = isChecked ? 'block' : 'none';
                if (companyNameInput) companyNameInput.required = isChecked;
                if (taxCodeInput) taxCodeInput.required = isChecked;
                if (companyAddressInput) companyAddressInput.required = isChecked;
            });
            isCompanyCheckbox.dispatchEvent(new Event('change'));
        }
    }

    function handleUserFormSubmit(e) {
        if (e) e.preventDefault();
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            genericModalPrimaryButton.disabled = false;
            genericModalPrimaryButton.textContent = document.getElementById('userId') && !!document.getElementById('userId').value ? 'Lưu thay đổi' : 'Thêm người dùng';
            return;
        }
        const userForm = document.getElementById('userForm');
        const errorEl = document.getElementById('userFormError');
        if (!userForm || !errorEl) return;

        errorEl.textContent = '';
        const userIdInput = document.getElementById('userId');
        const isEdit = userIdInput && !!userIdInput.value;

        genericModalPrimaryButton.disabled = true;
        genericModalPrimaryButton.textContent = isEdit ? 'Đang lưu...' : 'Đang thêm...';

        const fd = new FormData(userForm);
        fd.set('is_company', document.getElementById('isCompany').checked ? '1' : '0');

        const action = isEdit ? 'update_user' : 'create_user';

        postForm(`${apiBasePath}?action=${action}`, fd)
            .then(res => {
                if(res.success){
                    closeModal('genericModal');
                    window.showToast(res.message || (isEdit ? 'Cập nhật thành công!' : 'Thêm người dùng thành công!'), 'success');
                    location.reload();
                } else {
                    errorEl.textContent = res.message;
                    window.showToast(res.message, 'error');
                }
            })
            .catch(err => {
                errorEl.textContent = 'Lỗi khi gửi yêu cầu: ' + err.message;
                window.showToast('Đã có lỗi, hãy đảm bảo thông tin được nhập không bị trùng với tài khoản khác', 'error');
            })
            .finally(()=>{
                genericModalPrimaryButton.disabled = false;
                genericModalPrimaryButton.textContent = isEdit ? 'Lưu thay đổi' : 'Thêm người dùng';
            });
    }

    // Xử lý đổi mật khẩu
    function handlePasswordFormSubmit(e) {
        if (e) e.preventDefault();
        genericModalPrimaryButton.disabled = true;
        genericModalPrimaryButton.textContent = 'Đang lưu...';
        const errorEl = document.getElementById('passwordFormError');
        errorEl.textContent = '';
        const userId = document.getElementById('userIdPwd').value;
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        postJson(`${apiBasePath}?action=update_password`, { user_id: userId, password, confirm_password: confirm })
            .then(res => {
                if (res.success) {
                    closeModal('genericModal');
                    window.showToast(res.message || 'Đổi mật khẩu thành công!', 'success');
                } else {
                    errorEl.textContent = res.message;
                    window.showToast(res.message, 'error');
                }
            })
            .catch(err => {
                errorEl.textContent = 'Lỗi: ' + err.message;
                window.showToast('Lỗi khi gửi yêu cầu', 'error');
            })
            .finally(() => {
                genericModalPrimaryButton.disabled = false;
                genericModalPrimaryButton.textContent = 'Lưu';
            });
    }

    function viewUserDetails(userId) {
        genericModalTitle.textContent = 'Chi tiết Người dùng';
        genericModalBody.innerHTML = '<p>Đang tải...</p>';
        genericModalPrimaryButton.style.display = 'none';
        openModal('genericModal');

        getJson(`${apiBasePath}?action=get_user_details&id=${encodeURIComponent(userId)}`)
        .then(res => {
            if (res.success) {
                const u = res.data;
                let html = `
                    <div class="detail-row"><span class="detail-label">ID:</span> <span class="detail-value">${u.id}</span></div>
                    <div class="detail-row"><span class="detail-label">Tên đăng nhập:</span> <span class="detail-value">${u.username || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value">${u.email}</span></div>
                    <div class="detail-row"><span class="detail-label">Số điện thoại:</span> <span class="detail-value">${u.phone || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Loại tài khoản:</span> <span class="detail-value">${u.account_type_text}</span></div>
                    ${u.is_company == 1 ? `
                        <div class="detail-row"><span class="detail-label">Tên công ty:</span> <span class="detail-value">${u.company_name || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Mã số thuế:</span> <span class="detail-value">${u.tax_code || '-'}</span></div>
                        <div class="detail-row"><span class="detail-label">Địa chỉ công ty:</span> <span class="detail-value">${u.company_address || '-'}</span></div>
                    ` : ''}
                    <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${u.created_at_formatted}</span></div>
                    <div class="detail-row"><span class="detail-label">Cập nhật lần cuối:</span> <span class="detail-value">${u.updated_at_formatted || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${u.status_text}${u.deleted_at_formatted ? ' (' + u.deleted_at_formatted + ')' : ''}</span></div>
                `;
                genericModalBody.innerHTML = html;
            } else {
                genericModalBody.innerHTML = `<p style="color:red;">${res.message}</p>`;
            }
        })
        .catch(err => {
            genericModalBody.innerHTML = `<p style="color:red;">Lỗi tải dữ liệu: ${err.message}</p>`;
        });
    };

    function openEditUserModal(userId) {
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        genericModalTitle.textContent = 'Chỉnh sửa Người dùng';
        genericModalBody.innerHTML = getEditUserFormHTML();
        genericModalPrimaryButton.textContent = 'Lưu thay đổi';
        genericModalPrimaryButton.style.display = 'block';
        genericModalPrimaryButton.onclick = () => document.getElementById('userForm').requestSubmit();
        setupUserFormEventListeners('edit');

        const userForm = document.getElementById('userForm');
        const errorEl = document.getElementById('userFormError');
        if(userForm) userForm.reset();
        if(errorEl) errorEl.textContent = '';

        const passwordGroup = document.getElementById('passwordGroup');
        if (passwordGroup) passwordGroup.style.display = 'none'; 

        openModal('genericModal');
        getJson(`${apiBasePath}?action=get_user_details&id=${encodeURIComponent(userId)}`)
        .then(res => {
            if(res.success){
                const u = res.data;
                document.getElementById('userId').value = u.id;
                document.getElementById('username').value = u.username||'';
                document.getElementById('email').value    = u.email||'';
                document.getElementById('phone').value    = u.phone||'';
                const isCompanyCheckbox = document.getElementById('isCompany');
                if (isCompanyCheckbox) {
                    isCompanyCheckbox.checked = (u.is_company==1);
                    isCompanyCheckbox.dispatchEvent(new Event('change'));
                }
                if(u.is_company==1){
                    const companyNameInput = document.getElementById('companyName');
                    const taxCodeInput = document.getElementById('taxCode');
                    const companyAddressInput = document.getElementById('companyAddress');
                    if (companyNameInput) companyNameInput.value = u.company_name||'';
                    if (taxCodeInput) taxCodeInput.value = u.tax_code||'';
                    if (companyAddressInput) companyAddressInput.value = u.company_address||'';
                }
            } else {
                if(errorEl) errorEl.textContent = res.message;
                window.showToast(res.message, 'error');
                closeModal('genericModal');
            }
        })
        .catch(err => {
            if(errorEl) errorEl.textContent = 'Lỗi tải dữ liệu: ' + err.message;
            window.showToast('Lỗi tải dữ liệu: ' + err.message, 'error');
            closeModal('genericModal');
        });
    };

    // Mở modal đổi mật khẩu
    function openChangePasswordModal(userId) {
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        genericModalTitle.textContent = 'Đổi mật khẩu người dùng';
        genericModalBody.innerHTML = getChangePasswordFormHTML();
        genericModalPrimaryButton.style.display = 'block';
        genericModalPrimaryButton.textContent = 'Lưu';
        genericModalPrimaryButton.onclick = () => document.getElementById('passwordForm').requestSubmit();
        const passwordForm = document.getElementById('passwordForm');
        if (passwordForm) {
            passwordForm.reset();
            passwordForm.addEventListener('submit', handlePasswordFormSubmit);
        }
        const errorEl = document.getElementById('passwordFormError');
        if (errorEl) errorEl.textContent = '';
        document.getElementById('userIdPwd').value = userId;
        openModal('genericModal');
    }

    function openCreateUserModal() {
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        genericModalTitle.textContent = 'Thêm người dùng mới';
        genericModalBody.innerHTML = getCreateUserFormHTML();
        genericModalPrimaryButton.textContent = 'Thêm người dùng';
        genericModalPrimaryButton.style.display = 'block';
        genericModalPrimaryButton.onclick = () => document.getElementById('userForm').requestSubmit();
        setupUserFormEventListeners('create');

        const userForm = document.getElementById('userForm');
        const errorEl = document.getElementById('userFormError');
        if(userForm) userForm.reset();
        if(errorEl) errorEl.textContent = '';
        
        const passwordGroup = document.getElementById('passwordGroup');
        if (passwordGroup) passwordGroup.style.display = 'block'; 
        const passwordInput = document.getElementById('password');
        if (passwordInput) passwordInput.required = true;

        openModal('genericModal');
    };

    function toggleUserStatus(userId, action) {
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        const txt = action==='disable'?'vô hiệu hóa':'kích hoạt';
        if(!confirm(`Bạn có chắc muốn ${txt} người dùng ID ${userId}?`)) return;
        const body = new URLSearchParams({ user_id: userId, action });
        postForm(`${apiBasePath}?action=toggle_user_status`, body)
        .then(res => {
            if(res.success){
                window.showToast(res.message, 'success');
                location.reload();
            } else {
                window.showToast(res.message, 'error');
            }
        })
        .catch(err => {
            window.showToast(res.message, 'error');
        });
    };

    function bulkToggleUserStatus() {
        if (!canEditUsers) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
        const userIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (userIds.length === 0) {
            window.showToast('Vui lòng chọn ít nhất một người dùng.', 'warning');
            return;
        }

        if (!confirm(`Bạn có chắc muốn đảo ngược trạng thái của ${userIds.length} người dùng đã chọn?`)) {
            return;
        }

        const data = { 
            user_ids: userIds,
            bulk_operation: 'invert_status'
        };

        postJson(`${apiBasePath}?action=toggle_user_status`, data)
            .then(res => {
                if (res.success) {
                    window.showToast(res.message || 'Cập nhật trạng thái hàng loạt thành công!', 'success');
                    location.reload();
                } else {
                    if (res.data && res.data.errors && res.data.errors.length > 0) {
                        let errorMessages = res.data.errors.join('\n');
                        window.showToast(`${res.message}\nChi tiết:\n${errorMessages}`, 'error', 10000);
                    } else {
                        window.showToast(res.message || 'Có lỗi xảy ra khi cập nhật trạng thái.', 'error');
                    }
                }
            })
            .catch(err => {
                window.showToast('Lỗi gửi yêu cầu: ' + err.message, 'error');
            });
    }

    window.UserManagementPageEvents = {
        viewUserDetails,
        openEditUserModal,
        toggleUserStatus,
        openCreateUserModal,
        openChangePasswordModal,
        bulkToggleUserStatus
    };

    if (!canEditUsers) {
        const editButtons = document.querySelectorAll('button[data-permission="user_edit"], button[data-permission="user_create"]');
        editButtons.forEach(button => {
            button.disabled = true;
            button.style.cursor = 'not-allowed';
            button.title = 'Bạn không có quyền thực hiện hành động này.';
        });
        const actionColumnButtons = document.querySelectorAll('.action-buttons button:not(.btn-view)');
        actionColumnButtons.forEach(button => {
            button.disabled = true;
            button.style.cursor = 'not-allowed';
            button.title = 'Bạn không có quyền thực hiện hành động này.';
        });
    }
});
