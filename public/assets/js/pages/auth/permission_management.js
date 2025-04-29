(function(){
    const basePath     = window.basePath;
    const adminsData   = window.adminsData;
    const isSuperAdmin = window.isSuperAdmin;

    // Load current permissions on page load
    ['Admin','CustomerCare'].forEach(role => {
        fetch(`${basePath}public/actions/auth/index.php?action=fetch_permissions&role=${role.toLowerCase()}`)
            .then(res => res.json())
            .then(result => {
                if(result.success && Array.isArray(result.data)){
                    result.data.forEach(item=>{
                        const sel = `input[type="checkbox"][data-role="${role}"][data-permission="${item.permission}"]`;
                        const cb = document.querySelector(sel);
                        if(cb) cb.checked = item.allowed=='1'||item.allowed===1;
                    });
                }
            })
            .catch(err=>console.error(`Error fetching perms for ${role}:`,err));
    });

    function savePermissions(role, event){
        event.preventDefault();
        if(!isSuperAdmin){
            alert('Bạn không có quyền thực hiện hành động này.');
            return;
        }
        const permissions = {};
        document.querySelectorAll(`input[type="checkbox"][data-role="${role}"]`)
            .forEach(cb=> permissions[cb.dataset.permission]=cb.checked );
        fetch(`${basePath}public/actions/auth/index.php?action=process_permissions_update`, {
            method: 'POST',
            headers:{ 'Content-Type':'application/json' },
            body: JSON.stringify({ role: role==='Admin'?'admin':'customercare', permissions })
        })
        .then(r=>r.json())
        .then(data=>{
            alert(data.success ? data.message||'Cập nhật thành công!' : 'Lỗi: '+(data.message||'Không thể cập nhật.'));
        })
        .catch(err=>{ console.error(err); alert('Đã xảy ra lỗi.'); });
    }

    // Disable perms if not super admin
    if(!isSuperAdmin){
        document.querySelectorAll('#admin-permission-management input[type="checkbox"]:not([data-fixed-disabled])')
            .forEach(cb=>{
                cb.disabled = true;
                cb.style.cursor = 'not-allowed';
                const lbl = document.querySelector(`label[for="${cb.id}"]`);
                if(lbl){ lbl.style.cursor='not-allowed'; lbl.style.color='#6b7280'; }
            });
    }

    function openCreateRoleModal(){
        document.getElementById('createRoleForm').reset();
        document.getElementById('createRoleModal').style.display='flex';
    }

    function closeModal(id){
        const m = document.getElementById(id);
        if(m) m.style.display='none';
    }

    function openEditAdminModal(id){
        const admin = adminsData.find(a=>a.id==id);
        if(!admin) return;
        ['Id','Name','Username','Password','Role'].forEach(field=>{
            const el = document.getElementById(`editAdmin${field}`);
            if(el) el.value = field==='Username'? admin.admin_username : field==='Id'? admin.id : field==='Role'? admin.role : '';
        });
        document.getElementById('editAdminModal').style.display='flex';
    }

    function openDeleteAdminModal(id){
        document.getElementById('confirmDeleteAdminBtn').onclick = ()=> handleDeleteAdmin(id);
        document.getElementById('deleteAdminModal').style.display='flex';
    }

    async function handleDeleteAdmin(id){
        try{
            const res = await fetch(`${basePath}public/actions/auth/index.php?action=process_admin_delete`, {
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
                fetch(`${basePath}public/actions/auth/index.php?action=process_admin_create`,{
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body: JSON.stringify(data)
                })
                .then(r=>r.json())
                .then(res=>{
                    alert(res.success? res.message||'Tạo thành công!': 'Lỗi: '+(res.message||''));
                    if(res.success){ closeModal('createRoleModal'); location.reload(); }
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
                    const res = await fetch(`${basePath}public/actions/auth/index.php?action=process_admin_update`,{
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
        // expose globals for inline onclick attributes
        window.openCreateRoleModal  = openCreateRoleModal;
        window.openEditAdminModal   = openEditAdminModal;
        window.openDeleteAdminModal = openDeleteAdminModal;
        window.closeModal           = closeModal;
        window.savePermissions      = savePermissions;
    });
})();
