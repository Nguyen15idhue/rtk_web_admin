(function(){
    const adminsData   = window.adminsData;
    const isAdmin = window.isAdmin;
    console.log('Debug isAdmin value:', window.isAdmin, typeof window.isAdmin, '=>', isAdmin);

    window.togglePermissionGroup = function(headerElement, contentId) {
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
    };

    // Load current permissions on page load
    ['Admin','CustomerCare'].forEach(async role => {
        try {
            const result = await api.getJson(`${basePath}public/handlers/auth/index.php?action=fetch_permissions&role=${role.toLowerCase()}`);
            if (!result.success) throw new Error(result.message || 'Không thể tải quyền.');
            result.data.forEach(item => {
                const sel = `input[type="checkbox"][data-role="${role}"][data-permission="${item.permission}"]`;
                const cb = document.querySelector(sel);
                if (cb) cb.checked = item.allowed=='1'||item.allowed===1;
            });
        } catch (err) {
            console.error(err);
            window.showToast(`Error fetching perms for ${role}: ${err.message}`, 'error');
        }
    });

    async function savePermissions(role, event){
        event.preventDefault();
        if (!isAdmin) return window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
        const permissions = {};
        document.querySelectorAll(`input[type="checkbox"][data-role="${role}"]`)
                .forEach(cb => permissions[cb.dataset.permission] = cb.checked);
        try {
            const data = await api.postJson(`${basePath}public/handlers/auth/index.php?action=process_permissions_update`, {
                role: role==='Admin'?'admin':'customercare',
                permissions
            });
            window.showToast(data.message || 'Cập nhật thành công!', data.success?'success':'error');
        } catch (err) {
            console.error(err);
            window.showToast(`Lỗi cập nhật quyền: ${err.message}`, 'error');
        }
    }

    // Disable perms if not super admin
    if(!isAdmin){
        document.querySelectorAll('#admin-permission-management input[type="checkbox"]:not([data-fixed-disabled])')
            .forEach(cb=>{
                cb.disabled = true;
                cb.style.cursor = 'not-allowed';
                const lbl = document.querySelector(`label[for="${cb.id}"]`);
                if(lbl){ lbl.style.cursor='not-allowed'; lbl.style.color='#6b7280'; }
            });
    }

    const { closeModal: helperCloseModal } = window.helpers;

    function openCreateRoleModal(){
        document.getElementById('createRoleForm').reset();
        document.getElementById('createRoleModal').style.display='flex';
    }

    function openEditAdminModal(id){
        const admin = adminsData.find(a=>a.id==id);
        if(!admin) return;
        ['Id','Name','Username','Password','Role'].forEach(field=>{
            const el = document.getElementById(`editAdmin${field}`);
            if(!el) return;
            if(field==='Id')       el.value = admin.id;
            else if(field==='Name')     el.value = admin.name;
            else if(field==='Username') el.value = admin.admin_username;
            else if(field==='Password') el.value = '';
            else if(field==='Role')     el.value = admin.role;
        });
        document.getElementById('editAdminModal').style.display='flex';
    }

    function openDeleteAdminModal(id){
        document.getElementById('confirmDeleteAdminBtn').onclick = ()=> handleDeleteAdmin(id);
        document.getElementById('deleteAdminModal').style.display='flex';
    }

    async function handleDeleteAdmin(id){
        try{
            const res = await fetch(`${basePath}public/handlers/auth/index.php?action=process_admin_delete`, {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({id})
            });
            const result = await res.json();
            alert(result.message || (result.success?'Đã xóa':'Lỗi'));
            if(result.success) location.reload();
        }catch(err){
            console.error(err);
            alert('Lỗi khi xóa admin.');
        }
    }

    function openCreateCustomRoleModal() {
        const modal = document.getElementById('createCustomRoleModal');
        if (modal) {
            modal.style.display = 'block';
        }
        const form = document.getElementById('createCustomRoleForm');
        if (form) {
            form.reset(); // Reset form fields when opening
        }
    }

    // Wire up forms on DOMContentLoaded
    window.addEventListener('DOMContentLoaded',()=>{
        // create form
        const createForm = document.getElementById('createRoleForm');
        if(createForm){
            createForm.addEventListener('submit',e=>{
                e.preventDefault();
                const btn = createForm.querySelector('button[type="submit"]');
                btn.disabled=true;
                const data = {
                    name: createForm.name.value.trim(),
                    username: createForm.username.value.trim(),
                    password: createForm.password.value,
                    role: createForm.role.value
                };
                fetch(`${basePath}public/handlers/auth/index.php?action=process_admin_create`,{
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body: JSON.stringify(data)
                })
                .then(r=>r.json())
                .then(res=>{
                    alert(res.success? res.message||'Tạo thành công!': 'Lỗi: '+(res.message||''));
                    if(res.success){ helperCloseModal('createRoleModal'); location.reload(); }
                })
                .catch(err=>{ console.error(err); alert('Đã xảy ra lỗi.'); })
                .finally(()=> btn.disabled=false);
            });
        }
        // edit form
        const editForm = document.getElementById('editAdminForm');
        if(editForm){
            editForm.addEventListener('submit',async e=>{
                e.preventDefault();
                const payload = {
                    id:       document.getElementById('editAdminId').value,
                    name:     document.getElementById('editAdminName').value,
                    password: document.getElementById('editAdminPassword').value,
                    role:     document.getElementById('editAdminRole').value
                };
                try{
                    const res = await fetch(`${basePath}public/handlers/auth/index.php?action=process_admin_update`,{
                        method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)
                    });
                    const txt = await res.text();
                    let result;
                    try{ result = JSON.parse(txt); }
                    catch{ alert('Raw response:\n'+txt); return; }
                    alert(result.message||JSON.stringify(result));
                    if(result.success) location.reload();
                }catch(err){
                    console.error(err);
                    alert('Fetch error: '+err.message);
                }
            });
        }
        // Add event listener for the new custom role form
        const createCustomRoleForm = document.getElementById('createCustomRoleForm');
        if (createCustomRoleForm) {
            createCustomRoleForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                const formData = new FormData(createCustomRoleForm);
                const roleKey = formData.get('role_key');
                const roleName = formData.get('role_name');
                const permissions = formData.getAll('permissions[]');

                if (!roleKey || !/^[a-z0-9_]+$/.test(roleKey)) {
                    alert('Khóa Vai trò không hợp lệ. Chỉ cho phép chữ thường, số và dấu gạch dưới (_).');
                    return;
                }
                if (!roleName) {
                    alert('Tên Vai trò không được để trống.');
                    return;
                }

                console.log('Submitting new role:', { roleKey, roleName, permissions });
                try {
                    const response = await fetch(`${basePath}public/handlers/auth/index.php?action=process_role_create`, {
                        method: 'POST',
                        body: JSON.stringify({ role_key: roleKey, role_name: roleName, permissions: permissions }),
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message || 'Tạo vai trò mới thành công!');
                        helperCloseModal('createCustomRoleModal');
                        window.location.reload(); // Or update UI dynamically
                    } else {
                        alert('Lỗi: ' + (result.message || 'Không thể tạo vai trò.'));
                    }
                } catch (error) {
                    console.error('Error creating custom role:', error);
                    alert('Đã xảy ra lỗi khi tạo vai trò.');
                }
            });
        }
        // expose globals for inline onclick attributes
        window.PermissionPageEvents = {
            openCreateRoleModal,
            openEditAdminModal,
            openDeleteAdminModal,
            openCreateCustomRoleModal,
            closeModal: helperCloseModal,  // dùng chung
            savePermissions
        };
    });
})();
