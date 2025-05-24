(function(){
    let canEditPermissions; 
    let activeRoleKey = null;

    window.togglePermissionGroup = function(headerElement, contentId) {
        const contentElement = document.getElementById(contentId);
        const icon = headerElement.querySelector('i.fas');
        if (contentElement) {
            const isHidden = contentElement.style.display === 'none' || contentElement.style.display === '';
            contentElement.style.display = isHidden ? 'block' : 'none';
            if (icon) {
                icon.classList.toggle('fa-chevron-down', !isHidden);
                icon.classList.toggle('fa-chevron-up', isHidden);
            }
        }
    };

    const permissionPagesConfig = {}; // Will be populated by buildPermissionPagesData

    // Changed from IIFE to a regular function
    function buildPermissionPagesData(){
        // Access globals directly from window object as they are checked in initializePage
        if (typeof window.allDefinedPermissions === 'undefined' || typeof window.permissionGroupsConfig === 'undefined') {
            console.error('buildPermissionPagesData: Critical globals (allDefinedPermissions or permissionGroupsConfig) not ready. This should not happen if called after initializePage check.');
            return; 
        }

        for(const group in window.permissionGroupsConfig){
            const codes = window.permissionGroupsConfig[group];
            const seen = new Set();
            permissionPagesConfig[group] = []; // Populate the global permissionPagesConfig
            codes.forEach(code => {
                const m = code.match(/(.+?)_(view|edit)$/);
                const base = m ? m[1] : code;
                if(seen.has(base)) return;
                seen.add(base);
                const viewCode = window.allDefinedPermissions[base + '_view'] ? base + '_view' : null;
                const editCode = window.allDefinedPermissions[base + '_edit'] ? base + '_edit' : null;
                const label = window.allDefinedPermissions[viewCode] || window.allDefinedPermissions[editCode] || window.allDefinedPermissions[base] || base;
                permissionPagesConfig[group].push({ base, viewCode, editCode, label });
            });
        }
        console.log('buildPermissionPagesData: permissionPagesConfig populated:', permissionPagesConfig);
    }

    function renderPermissionsModal(roleKey) {
        activeRoleKey = roleKey;
        const modal = document.getElementById('permissionsConfigModal');
        const modalBody = document.getElementById('permissionsModalBody');
        const modalRoleName = document.getElementById('modalRoleName');
        const saveBtn = document.getElementById('saveRolePermissionsBtn');

        if (!modal || !modalBody || !modalRoleName || !window.currentRolePermissions[roleKey]) { // Use window.currentRolePermissions
            return;
        }

        modalRoleName.textContent = window.roleDisplayNames[roleKey] || roleKey; // Use window.roleDisplayNames
        modalBody.innerHTML = ''; // Clear previous content

        let groupRenderIndex = 0; // Used for generating unique IDs

        // Iterate over permissionGroupsConfig to maintain defined group order
        for (const groupName in window.permissionGroupsConfig) { // Use window.permissionGroupsConfig
            const pages = permissionPagesConfig[groupName]; // Uses the populated permissionPagesConfig
            
            // Only render a section if there are view/edit "pages" for this group
            if (!pages || !pages.length) {
                continue;
            }
            
            groupRenderIndex++;
            const groupEl = document.createElement('div');
            groupEl.className = 'permission-group-modal mb-6 p-4 border border-gray-200 rounded-lg shadow-sm bg-white';
            
            const contentId = `modal-config-group-content-${roleKey}-${groupRenderIndex}`;

            const headerElement = document.createElement('div');
            headerElement.className = 'permission-group-header font-semibold text-gray-800 text-md mb-3 cursor-pointer flex justify-between items-center';
            headerElement.onclick = function() { window.togglePermissionGroup(this, contentId); };
            headerElement.innerHTML = `
                <span>${groupName}</span>
                <i class="fas fa-chevron-down text-gray-500"></i>
            `;

            const contentElement = document.createElement('div');
            contentElement.id = contentId;
            contentElement.className = 'permission-group-content';
            contentElement.style.display = 'none'; // Start collapsed

            const tableWrapper = document.createElement('div');
            tableWrapper.className = 'overflow-x-auto rounded-lg border border-gray-300';
            
            const tableHtmlHeader = `
                  <table class="min-w-full table-fixed">
                    <colgroup>
                        <col style="width: auto;">
                        <col style="width: 112px;"> 
                        <col style="width: 112px;">
                        <col style="width: 112px;">
                    </colgroup>
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quyền</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Không</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Chỉ xem</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Được sửa</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">`;
            const rowsHtml = pages.map(page => {
                const { base, viewCode, editCode, label } = page;
                const hasView = viewCode && window.currentRolePermissions[roleKey][viewCode]?.allowed; // Use window.currentRolePermissions
                const hasEdit = editCode && window.currentRolePermissions[roleKey][editCode]?.allowed; // Use window.currentRolePermissions
                const state = hasEdit ? 'edit' : (hasView ? 'view' : 'none');
                const disabledAttr = !canEditPermissions ? 'disabled style="cursor:not-allowed;"' : '';

                return `
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-800 truncate">${label}</td>
                    <td style="padding:0;">
                      <div style="display:flex; justify-content:center; align-items:center; min-height:48px; min-width:112px;">
                        <input type="radio" name="perm_${roleKey}_${base}" value="none" ${state==='none'?'checked':''} ${disabledAttr}
                               class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                               onchange="PermissionPageEvents.handlePermissionModeChange('${roleKey}','${base}','none')">
                      </div>
                    </td>
                    <td style="padding:0;">
                      <div style="display:flex; justify-content:center; align-items:center; min-height:48px; min-width:112px;">
                        <input type="radio" name="perm_${roleKey}_${base}" value="view" ${state==='view'?'checked':''} ${disabledAttr}
                               class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                               onchange="PermissionPageEvents.handlePermissionModeChange('${roleKey}','${base}','view')">
                      </div>
                    </td>
                    ${ editCode
                      ? `<td style="padding:0;">
                           <div style="display:flex; justify-content:center; align-items:center; min-height:48px; min-width:112px;">
                             <input type="radio" name="perm_${roleKey}_${base}" value="edit" ${state==='edit'?'checked':''} ${disabledAttr}
                                    class="form-radio h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                                    onchange="PermissionPageEvents.handlePermissionModeChange('${roleKey}','${base}','edit')">
                           </div>
                         </td>`
                      : `<td></td>` }
                  </tr>`;
            }).join('');
            const tableHtmlFooter = `
                    </tbody>
                  </table>`;
            tableWrapper.innerHTML = tableHtmlHeader + rowsHtml + tableHtmlFooter;
            contentElement.appendChild(tableWrapper);
            
            groupEl.appendChild(headerElement);
            groupEl.appendChild(contentElement);
            modalBody.appendChild(groupEl);
        }

        const ungroupedPermissionsContainer = document.createElement('div');
        ungroupedPermissionsContainer.className = 'permission-group-modal mb-4 p-3 border border-gray-200 rounded-lg shadow-sm';
        const ungroupedContentId = `modal-group-content-${roleKey}-ungrouped`;
        let ungroupedPermissionsHTML = '';
        let hasUngrouped = false;

        const allGroupedPermissions = new Set();
        Object.values(window.permissionGroupsConfig).forEach(group => group.forEach(p => allGroupedPermissions.add(p))); // Use window.permissionGroupsConfig

        for (const permCode in window.allDefinedPermissions) { // Use window.allDefinedPermissions
            if (window.allDefinedPermissions.hasOwnProperty(permCode) && !allGroupedPermissions.has(permCode)) {
                 if (window.currentRolePermissions[roleKey] && window.currentRolePermissions[roleKey].hasOwnProperty(permCode)) { // Use window.currentRolePermissions
                    hasUngrouped = true;
                    const permDescription = window.allDefinedPermissions[permCode]; // Use window.allDefinedPermissions
                    const currentPermissionState = window.currentRolePermissions[roleKey][permCode].allowed; // Use window.currentRolePermissions
                     const isCoreAdminLocked = roleKey === 'admin' && (permCode === 'dashboard' || permCode.startsWith('permission_management'));

                    ungroupedPermissionsHTML += `
                        <div class="permission-item py-2 px-3 mb-2 bg-gray-50 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-150">
                            <label class="flex items-center justify-between text-sm text-gray-700 cursor-pointer">
                                <span>${permDescription}</span>
                                <input type="checkbox" 
                                       class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       data-role="${roleKey}" 
                                       data-permission="${permCode}"
                                       ${currentPermissionState ? 'checked' : ''}
                                       ${isCoreAdminLocked || !canEditPermissions ? 'disabled' : ''}
                                       ${!canEditPermissions ? 'style="cursor:not-allowed;"' : ''}
                                       onchange="PermissionPageEvents.handlePermissionChange(this, '${roleKey}', '${permCode}')">
                            </label>
                            ${!canEditPermissions && !isCoreAdminLocked ? '<p class="text-xs text-gray-500 mt-1">Chỉ người có quyền mới có thể thay đổi.</p>' : ''}
                        </div>
                    `;
                }
            }
        }

        if (hasUngrouped) {
            ungroupedPermissionsContainer.innerHTML = `
                <h5 class="permission-group-header font-semibold text-gray-800 text-md mb-3 cursor-pointer flex justify-between items-center" 
                    onclick="togglePermissionGroup(this, '${ungroupedContentId}')">
                    <span>Quyền Khác</span>
                    <i class="fas fa-chevron-down text-gray-500"></i>
                </h5>
                <div id="${ungroupedContentId}" class="permission-group-content space-y-2" style="display: none;"> 
                    ${ungroupedPermissionsHTML}
                </div>
            `;
            modalBody.appendChild(ungroupedPermissionsContainer);
        }

        window.helpers.openModal && window.helpers.openModal('permissionsConfigModal'); // Use window.helpers

        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.opacity = '0';
            modalContent.style.transform = 'translate(-50%, -50%) scale(0.95)';

            setTimeout(() => {
                modalContent.style.opacity = '1';
                modalContent.style.transform = 'translate(-50%, -50%) scale(1)';
            }, 10);
        }

        saveBtn.disabled = !canEditPermissions;
        if (!canEditPermissions) {
            saveBtn.style.cursor = 'not-allowed';
            saveBtn.title = 'Bạn không có quyền thực hiện hành động này.';
        } else {
            saveBtn.style.cursor = 'pointer';
            saveBtn.title = '';
        }
    }

    function closePermissionsModal() {
        window.helpers.closeModal && window.helpers.closeModal('permissionsConfigModal'); // Use window.helpers
        activeRoleKey = null;
    }

    function handlePermissionChange(checkbox, roleKey, permCode) {
        if (!canEditPermissions) return;
        if (!window.currentRolePermissions[roleKey]) { // Use window.currentRolePermissions
            window.currentRolePermissions[roleKey] = {};
        }
        if (!window.currentRolePermissions[roleKey][permCode]) { // Use window.currentRolePermissions
            window.currentRolePermissions[roleKey][permCode] = { description: window.allDefinedPermissions[permCode] }; // Use window.allDefinedPermissions
        }
        window.currentRolePermissions[roleKey][permCode].allowed = checkbox.checked; // Use window.currentRolePermissions
    }

    function handlePermissionModeChange(roleKey, base, mode) {
        if(!canEditPermissions) return;
        ['view','edit'].forEach(type => {
            const code = base + '_' + type;
            if(!window.currentRolePermissions[roleKey][code]) { // Use window.currentRolePermissions
                window.currentRolePermissions[roleKey][code] = { allowed: false, description: window.allDefinedPermissions[code]||code }; // Use window.allDefinedPermissions
            }
            window.currentRolePermissions[roleKey][code].allowed = (mode === type) || (type==='view' && mode==='edit'); // Use window.currentRolePermissions
        });
    }

    async function saveRolePermissions() {
        if (!canEditPermissions || !activeRoleKey) {
            window.showToast('Bạn không có quyền thực hiện hoặc không có vai trò nào được chọn.', 'error');
            return;
        }

        const permissionsToSave = {};
        if (window.currentRolePermissions[activeRoleKey]) { // Use window.currentRolePermissions
            for (const permCode in window.currentRolePermissions[activeRoleKey]) { // Use window.currentRolePermissions
                if (window.currentRolePermissions[activeRoleKey].hasOwnProperty(permCode)) { // Use window.currentRolePermissions
                    permissionsToSave[permCode] = window.currentRolePermissions[activeRoleKey][permCode].allowed; // Use window.currentRolePermissions
                }
            }
        }

        try {
            const data = await window.api.postJson(`${window.basePath}public/handlers/auth/index.php?action=process_permissions_update`, { // Use window.api and window.basePath
                role: activeRoleKey,
                permissions: permissionsToSave
            });
            window.showToast(data.message || 'Cập nhật quyền thành công!', data.success ? 'success' : 'error');
            if (data.success) {
                closePermissionsModal();
            }
        } catch (err) {
            window.showToast(`Lỗi cập nhật quyền: ${err.message}`, 'error');
        }
    }

    function openCreateRoleModal(){
        if (!canEditPermissions) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        const form = document.getElementById('createRoleForm');
        if (form) form.reset();
        window.helpers.openModal && window.helpers.openModal('createRoleModal'); // Use window.helpers
    }

    function openEditAdminModal(id){
        if (!canEditPermissions) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        const admin = window.adminsData.find(a=>a.id==id); // Use window.adminsData
        if(!admin) return;
        ['Id','Name','Username','Password','Role'].forEach(field=>{
            const el = document.getElementById(`editAdmin${field}`);
            if(!el) return;
            if(field==='Id')       el.value = admin.id;
            else if(field==='Name') el.value = admin.name;
            else if(field==='Username') el.value = admin.admin_username;
            else if(field==='Password') el.value = '';
            else if(field==='Role') el.value = admin.role;
        });
        window.helpers.openModal && window.helpers.openModal('editAdminModal'); // Use window.helpers
    }

    function openDeleteAdminModal(id){
        if (!canEditPermissions) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        const admin = window.adminsData.find(a => a.id == id); // Use window.adminsData
        if (!admin) {
            window.showToast('Không tìm thấy thông tin tài khoản admin.', 'error');
            return;
        }

        const modalTextElement = document.getElementById('deleteAdminName');
        if (modalTextElement) {
            modalTextElement.textContent = admin.name || admin.admin_username;
        }

        const confirmBtn = document.getElementById('confirmDeleteAdminBtn');
        if (confirmBtn) confirmBtn.onclick = ()=> handleDeleteAdmin(id);
        window.helpers.openModal && window.helpers.openModal('deleteAdminModal'); // Use window.helpers
    }

    async function handleDeleteAdmin(id){
        if (!canEditPermissions) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        try{
            const res = await fetch(`${window.basePath}public/handlers/auth/index.php?action=process_admin_delete`, { // Use window.basePath
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({id})
            });
            const result = await res.json();
            window.showToast(result.message || (result.success ? 'Đã xóa thành công.' : 'Lỗi khi xóa.'), result.success ? 'success' : 'error');
            if(result.success) setTimeout(()=>location.reload(), 1500);
        }catch(err){
            window.showToast('Lỗi kết nối khi xóa admin.', 'error');
        }
    }

    function openCreateCustomRoleModal() {
        if (!canEditPermissions) {
            window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
            return;
        }
        window.helpers.openModal && window.helpers.openModal('createCustomRoleModal'); // Use window.helpers
        const form = document.getElementById('createCustomRoleForm');
        if (form) {
            form.reset();
            form.querySelectorAll('.permission-group-content').forEach(el => el.style.display = 'none');
            form.querySelectorAll('.permission-group-header i.fas').forEach(icon => {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            });
        }
    }
    
    function activateTab(selectedButton) {
        document.querySelectorAll('.role-tab-button').forEach(button => {
            button.classList.remove('text-blue-700', 'border-blue-600', 'font-semibold', 'btn-primary');
            button.classList.add('text-gray-600', 'border-transparent', 'btn-secondary');
        });
        selectedButton.classList.add('text-blue-700', 'border-blue-600', 'font-semibold', 'btn-primary');
        selectedButton.classList.remove('text-gray-600', 'border-transparent', 'btn-secondary');
        
        const roleKey = selectedButton.dataset.roleKey;
        renderPermissionsModal(roleKey);
    }

    window.addEventListener('DOMContentLoaded',()=>{
        function initializePage() {
            // Check for essential global variables
            if (typeof window.appConfig === 'undefined' ||
                typeof window.roleDisplayNames === 'undefined' ||
                typeof window.allDefinedPermissions === 'undefined' ||
                typeof window.currentRolePermissions === 'undefined' ||
                typeof window.permissionGroupsConfig === 'undefined' ||
                typeof window.adminsData === 'undefined' || 
                typeof window.helpers === 'undefined' || 
                typeof window.api === 'undefined' ||
                typeof window.basePath === 'undefined' // Added basePath check
            ) {
                console.warn('Essential global variables not yet defined. Retrying initialization in 100ms.');
                setTimeout(initializePage, 100);
                return;
            }
            
            console.log('All essential global variables are defined. Proceeding with page initialization.');

            // Call buildPermissionPagesData now that globals are ready
            buildPermissionPagesData();

            // Initialize canEditPermissions here
            canEditPermissions = window.appConfig && window.appConfig.permissions && window.appConfig.permissions.permission_management_edit;
            console.log('initializePage: canEditPermissions initialized to:', canEditPermissions, 'from window.appConfig:', window.appConfig);

            // Moved button disabling logic here
            if(!canEditPermissions){
                const createRoleBtn = document.querySelector('button[onclick*="openCreateCustomRoleModal()"]');
                if (createRoleBtn) {
                    createRoleBtn.disabled = true;
                    createRoleBtn.style.cursor = 'not-allowed';
                    createRoleBtn.title = "Bạn không có quyền thực hiện hành động này.";
                }
                const addAdminBtn = document.querySelector('button[onclick*="openCreateRoleModal()"]');
                if (addAdminBtn) {
                    addAdminBtn.disabled = true;
                    addAdminBtn.style.cursor = 'not-allowed';
                    addAdminBtn.title = "Bạn không có quyền thực hiện hành động này.";
                }
                const adminActionButtons = document.querySelectorAll('#adminAccountsTable .actions button');
                adminActionButtons.forEach(button => {
                    button.disabled = true;
                    button.style.cursor = 'not-allowed';
                    button.title = "Bạn không có quyền thực hiện hành động này.";
                });
            }

            // MOVED: Initial permission fetching loop
            // Ensure roleDisplayNames is populated before iterating
            if (window.roleDisplayNames && Object.keys(window.roleDisplayNames).length > 0) {
                Object.keys(window.roleDisplayNames).forEach(async roleKey => {
                    if (!window.currentRolePermissions[roleKey]) { // Use window.currentRolePermissions
                        window.currentRolePermissions[roleKey] = {}; // Use window.currentRolePermissions
                    }
                    try {
                        // Ensure window.api is available
                        if (!window.api || typeof window.api.getJson !== 'function') {
                            console.error('window.api.getJson is not available for fetching permissions.');
                            return;
                        }
                        const result = await window.api.getJson(`${window.basePath}public/handlers/auth/index.php?action=fetch_permissions&role=${roleKey.toLowerCase()}`); // Use window.api and window.basePath
                        if (!result.success) throw new Error(result.message || 'Không thể tải quyền.');
                        
                        result.data.forEach(item => {
                            if (!window.currentRolePermissions[roleKey][item.permission]) { // Use window.currentRolePermissions
                                 window.currentRolePermissions[roleKey][item.permission] = {  // Use window.currentRolePermissions
                                    description: window.allDefinedPermissions[item.permission] || item.permission, // Use window.allDefinedPermissions
                                    allowed: item.allowed 
                                };
                            } else {
                                window.currentRolePermissions[roleKey][item.permission].allowed = item.allowed; // Use window.currentRolePermissions
                            }
                        });
                    } catch (err) {
                        console.error(`Lỗi khi tải quyền cho vai trò ${roleKey}:`, err);
                        if (window.showToast) {
                            window.showToast(`Không thể tải quyền cho vai trò "${window.roleDisplayNames[roleKey] || roleKey}". Lỗi: ${err.message}`, 'error'); // Use window.roleDisplayNames
                        }
                    }
                });
            } else {
                console.warn('roleDisplayNames is not populated. Skipping initial permission fetch.');
            }


            const roleTabButtons = document.querySelectorAll('.role-tab-button');
            roleTabButtons.forEach(button => {
                button.classList.add('btn', 'btn-secondary'); 
                button.addEventListener('click', () => activateTab(button));
            });

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
                    if (!data.name || !data.username || !data.password || !data.role) {
                        window.showToast('Vui lòng điền đầy đủ thông tin.', 'error');
                        btn.disabled = false;
                        return;
                    }
                    fetch(`${window.basePath}public/handlers/auth/index.php?action=process_admin_create`,{
                        method:'POST',
                        headers:{'Content-Type':'application/json'},
                        body: JSON.stringify(data)
                    })
                    .then(r=>r.json())
                    .then(res=>{
                        window.showToast(res.message || (res.success ? 'Tạo tài khoản thành công!' : 'Lỗi!'), res.success ? 'success' : 'error');
                        if (res.success) {
                            window.helpers.closeModal && window.helpers.closeModal('createRoleModal'); // Use window.helpers
                            setTimeout(()=>location.reload(), 1500);
                        }
                    })
                    .catch(err=>{
                        window.showToast('Lỗi kết nối: Không thể tạo tài khoản.', 'error');
                    })
                    .finally(()=> btn.disabled=false);
                });
            }

            const editForm = document.getElementById('editAdminForm');
            if(editForm){
                editForm.addEventListener('submit',async e=>{
                    e.preventDefault();
                    const btn = editForm.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    const data = {
                        id: editForm.id.value,
                        name: editForm.name.value.trim(),
                        password: editForm.password.value,
                        role: editForm.role.value
                    };
                    if (!data.name || !data.role) {
                        window.showToast('Tên và Vai trò không được để trống.', 'error');
                        btn.disabled = false;
                        return;
                    }
                    try {
                        const response = await window.api.postJson(`${window.basePath}public/handlers/auth/index.php?action=process_admin_update`, data); // Use window.api and window.basePath
                        window.showToast(response.message || 'Cập nhật thành công!', response.success ? 'success' : 'error');
                        if (response.success) {
                            window.helpers.closeModal && window.helpers.closeModal('editAdminModal'); // Use window.helpers
                            setTimeout(()=>location.reload(), 1500);
                        }
                    } catch (err) {
                        window.showToast('Lỗi kết nối: ' + err.message, 'error');
                    } finally {
                        btn.disabled = false;
                    }
                });
            }

            const createCustomRoleForm = document.getElementById('createCustomRoleForm');
            if (createCustomRoleForm) {
                createCustomRoleForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    if (!canEditPermissions) {
                        window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                        return;
                    }
                    const btn = createCustomRoleForm.querySelector('button[type="submit"]');
                    btn.disabled = true;

                    const roleName = createCustomRoleForm.role_name.value.trim();
                    const roleKey = createCustomRoleForm.role_key.value.trim().toLowerCase().replace(/\s+/g, '_');
                    
                    const selectedPermissions = [];
                    createCustomRoleForm.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                        if (radio.value !== 'none') {
                            const permission = radio.getAttribute('data-permission');
                            if (permission) { // Ensure data-permission attribute exists
                                selectedPermissions.push(permission);
                            }
                        }
                    });

                    if (!roleName || !roleKey) {
                        window.showToast('Tên vai trò và Khóa vai trò không được để trống.', 'error');
                        btn.disabled = false;
                        return;
                    }
                    if (!/^[a-z0-9_]+$/.test(roleKey)) {
                        window.showToast('Khóa vai trò chỉ được chứa chữ thường, số và dấu gạch dưới (_).', 'error');
                        btn.disabled = false;
                        return;
                    }
                     if (selectedPermissions.length === 0) {
                        window.showToast('Vui lòng chọn ít nhất một quyền cho vai trò mới.', 'error');
                        btn.disabled = false;
                        return;
                    }

                    try {
                        const response = await window.api.postJson(`${window.basePath}public/handlers/auth/index.php?action=process_role_create`, { // Use window.api and window.basePath
                            role_name: roleName,
                            role_key: roleKey,
                            permissions: selectedPermissions
                        });
                        window.showToast(response.message || 'Tạo vai trò thành công!', response.success ? 'success' : 'error');
                        if (response.success) {
                            window.helpers.closeModal && window.helpers.closeModal('createCustomRoleModal'); // Use window.helpers
                            setTimeout(()=>location.reload(), 1500);
                        }
                    } catch (err) {
                        console.error('Error creating custom role:', err);
                        window.showToast(`Lỗi tạo vai trò: ${err.message}`, 'error');
                    } finally {
                        btn.disabled = false;
                    }
                });
            }

            window.PermissionPageEvents = {
                openCreateRoleModal,
                openEditAdminModal,
                openDeleteAdminModal,
                openCreateCustomRoleModal,
                closeModal: (modalId) => window.helpers.closeModal && window.helpers.closeModal(modalId), // Use window.helpers
                saveRolePermissions,
                closePermissionsModal,
                handlePermissionChange,
                handlePermissionModeChange
                // initializePage is internal to this event listener, not needed on PermissionPageEvents
            };

            // Attach click handler to the Save Permissions button
            const saveBtn = document.getElementById('saveRolePermissionsBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', window.PermissionPageEvents.saveRolePermissions);
            }
        } // End of initializePage function

        initializePage(); // Call initializePage to start the process
    });
})();
