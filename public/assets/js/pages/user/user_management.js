document.addEventListener('DOMContentLoaded', ()=> {
    const { basePath } = window.appConfig;
    const viewBody = document.getElementById('viewUserDetailsBody');

    // open/close helpers
    const { closeModal, toggleCompanyFields } = window.helpers;
    const { getJson, postJson, postForm }      = window.api;

    // Thay thế base path cho API
    const apiBasePath = '../../actions/user/index.php';

    // CREATE USER
    const createForm = document.getElementById('createUserForm');
    createForm.addEventListener('submit', e => {
        e.preventDefault();
        const errEl = document.getElementById('createUserError');
        errEl.textContent = '';
        const btn = createForm.querySelector('button[type="submit"]');
        btn.disabled = true; btn.textContent = 'Đang thêm...';

        const fd = new FormData(createForm);
        if (!fd.has('is_company')) fd.set('is_company', '0');

        // map formdata to plain object with correct is_company
        const data = {};
        fd.forEach((v,k) => {
            if (k === 'is_company') {
                data[k] = (v === 'on' ? 1 : 0);
            } else {
                data[k] = v;
            }
        });

        postJson(`${apiBasePath}?action=create_user`, data)
        .then(res => {
            if(res.success){
                closeModal('createUserModal');
                window.showToast(res.message || 'Thêm người dùng thành công!', 'success');
                location.reload();
            } else {
                errEl.textContent = res.message;
                window.showToast(res.message, 'error');
            }
        })
        .catch(err => {
            errEl.textContent = 'Lỗi khi gửi yêu cầu: ' + err.message;
        })
        .finally(()=>{
            btn.disabled = false; btn.textContent = 'Thêm người dùng';
        });
    });

    // VIEW DETAILS
    window.viewUserDetails = userId => {
        viewBody.innerHTML = '<p>Đang tải...</p>';
        document.getElementById('viewUserModal').style.display = 'block';

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
                    ` : ''}
                    <div class="detail-row"><span class="detail-label">Ngày tạo:</span> <span class="detail-value">${u.created_at_formatted}</span></div>
                    <div class="detail-row"><span class="detail-label">Cập nhật lần cuối:</span> <span class="detail-value">${u.updated_at_formatted || '-'}</span></div>
                    <div class="detail-row"><span class="detail-label">Trạng thái:</span> <span class="detail-value">${u.status_text}${u.deleted_at_formatted ? ' (' + u.deleted_at_formatted + ')' : ''}</span></div>
                `;
                viewBody.innerHTML = html;
            } else {
                viewBody.innerHTML = `<p style="color:red;">${res.message}</p>`;
            }
        })
        .catch(err => {
            viewBody.innerHTML = `<p style="color:red;">Lỗi tải dữ liệu: ${err.message}</p>`;
        });
    };

    // EDIT USER
    const editForm = document.getElementById('editUserForm');
    window.openEditUserModal = userId => {
        ['Username','Email','Phone'].forEach(f=> document.getElementById('edit'+f).value = '');
        toggleCompanyFields('edit');
        document.getElementById('editUserModal').style.display = 'block';
        getJson(`${apiBasePath}?action=get_user_details&id=${encodeURIComponent(userId)}`)
        .then(res => {
            if(res.success){
                const u = res.data;
                document.getElementById('editUserId').value = u.id;
                document.getElementById('editUsername').value = u.username||'';
                document.getElementById('editEmail').value    = u.email||'';
                document.getElementById('editPhone').value    = u.phone||'';
                document.getElementById('editIsCompany').checked = (u.is_company==1);
                toggleCompanyFields('edit');
                if(u.is_company==1){
                    document.getElementById('editCompanyName').value = u.company_name||'';
                    document.getElementById('editTaxCode').value    = u.tax_code||'';
                }
            } else {
                document.getElementById('editUserError').textContent = res.message;
            }
        })
        .catch(err => {
            document.getElementById('editUserError').textContent = 'Lỗi tải dữ liệu: ' + err.message;
        });
    };

    editForm.addEventListener('submit', e => {
        e.preventDefault();
        const errEl = document.getElementById('editUserError');
        errEl.textContent = '';
        const btn = editForm.querySelector('button[type="submit"]');
        btn.disabled = true; btn.textContent = 'Đang lưu...';

        const fd = new FormData(editForm);
        fd.set('is_company', fd.has('is_company')?'1':'0');

        postForm(`${apiBasePath}?action=update_user`, fd)
        .then(res => {
            if(res.success){
                window.showToast(res.message, 'success');
                closeModal('editUserModal');
                location.reload();
            } else {
                errEl.textContent = res.message;
                window.showToast(res.message, 'error');
            }
        })
        .catch(err => {
            errEl.textContent = 'Lỗi gửi yêu cầu: ' + err.message;
        })
        .finally(()=>{
            btn.disabled = false; btn.textContent = 'Lưu thay đổi';
        });
    });

    // TOGGLE STATUS
    window.toggleUserStatus = (userId, action) => {
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

    // OPEN CREATE
    window.openCreateUserModal = () => {
        // reset form state
        createForm.reset();
        document.getElementById('createUserError').textContent = '';
        toggleCompanyFields('create');
        document.getElementById('createUserModal').style.display = 'block';
    };

    // bind company‑checkbox visibility
    ['edit','create'].forEach(t => {
        document.getElementById(t+'IsCompany')
                .addEventListener('change', ()=> toggleCompanyFields(t));
    });
});
