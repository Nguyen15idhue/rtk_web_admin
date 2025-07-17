// Select All logic is now handled by utils/bulk_actions.js

// manager modal helper functions

// Use basePath defined in the PHP view (e.g., station_management.php)
const managerHandlerUrl = basePath + '/public/handlers/station/manager_index.php';
const stationHandlerUrl = basePath + '/public/handlers/station/index.php';
const canEditStation = window.appConfig && window.appConfig.permissions && window.appConfig.permissions.station_management_edit;

// Auto-save mountpoint location when selection changes
function setupMountpointAutoSave() {
    const mountpointSelects = document.querySelectorAll('.mountpoint-location-select');
    
    mountpointSelects.forEach(select => {
        // Skip if already has event listener
        if (select.hasAttribute('data-auto-save-initialized')) return;
        select.setAttribute('data-auto-save-initialized', 'true');
        
        if (!canEditStation) return; // Skip if user doesn't have edit permission
        
        select.addEventListener('change', async function() {
            const mountpointId = this.getAttribute('data-mountpoint-id');
            const locationId = this.value;
            
            try {
                // Get the API base path
                const mountpointApiBasePath = basePath + '/public/handlers/station/mountpoint_index.php';
                
                // Prepare form data
                const formData = new FormData();
                formData.append('action', 'update_mountpoint');
                formData.append('mountpoint_id', mountpointId);
                formData.append('location_id', locationId);
                
                // Send AJAX request with proper headers
                const response = await fetch(`${mountpointApiBasePath}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                // Parse JSON response
                const result = await response.json();
                
                if (result.success) {
                    // Show toast notification
                    if (window.showToast) {
                        window.showToast(result.message || 'Đã cập nhật Mount Point thành công', 'success');
                    }
                } else {
                    throw new Error(result.message || 'Cập nhật thất bại');
                }
                
            } catch (error) {
                console.error('Error updating mountpoint:', error);
                
                // Show error toast
                if (window.showToast) {
                    window.showToast('Lỗi khi cập nhật Mount Point', 'error');
                }
                
                // Optionally revert the selection
                // this.selectedIndex = this.getAttribute('data-previous-index') || 0;
            }
        });
        
        // Store the initial selected index for potential revert
        select.setAttribute('data-previous-index', select.selectedIndex);
        select.addEventListener('focus', function() {
            this.setAttribute('data-previous-index', this.selectedIndex);
        });
    });
}

// Auto-save station information when selection changes
function setupStationAutoSave() {
    const managerSelects = document.querySelectorAll('.station-manager-select');
    const mountpointSelects = document.querySelectorAll('.station-mountpoint-select');
    
    // Debug info
    console.log('Setting up station auto-save:', {
        managerSelects: managerSelects.length,
        mountpointSelects: mountpointSelects.length,
        canEditStation: canEditStation
    });
    
    // Helper function to handle station updates
    async function updateStation(stationId, managerName, mountpointDetails) {
        try {
            // Validate required parameters
            if (!stationId) {
                throw new Error('Station ID is required');
            }
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'update_station');
            formData.append('station_id', stationId);
            formData.append('manager_name', managerName || '');
            formData.append('mountpoint_details', mountpointDetails || '');
            
            // Send AJAX request with proper headers
            const response = await fetch(`${stationHandlerUrl}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Parse JSON response
            const result = await response.json();
            
            if (result.success) {
                // Show toast notification
                if (window.showToast) {
                    window.showToast(result.message || 'Đã cập nhật thông tin trạm thành công', 'success');
                }
            } else {
                throw new Error(result.message || 'Cập nhật thất bại');
            }
            
        } catch (error) {
            console.error('Error updating station:', error);
            
            // Show error toast
            if (window.showToast) {
                window.showToast('Lỗi khi cập nhật thông tin trạm: ' + error.message, 'error');
            }
            
            throw error; // Re-throw for caller to handle
        }
    }
    
    // Setup manager select auto-save
    managerSelects.forEach(select => {
        // Skip if already has event listener
        if (select.hasAttribute('data-auto-save-initialized')) return;
        select.setAttribute('data-auto-save-initialized', 'true');
        
        if (!canEditStation) return; // Skip if user doesn't have edit permission
        
        select.addEventListener('change', async function() {
            const stationId = this.getAttribute('data-station-id');
            const managerName = this.value;
            
            if (!stationId) {
                console.error('Station ID not found for manager select');
                return;
            }
            
            // Get current mountpoint value from the same row
            const row = this.closest('tr');
            if (!row) {
                console.error('Could not find table row for manager select');
                return;
            }
            
            const mountpointSelect = row.querySelector('.station-mountpoint-select');
            const mountpointDetails = mountpointSelect ? mountpointSelect.value : '';
            
            try {
                await updateStation(stationId, managerName, mountpointDetails);
            } catch (error) {
                // Optionally revert the selection
                // this.selectedIndex = this.getAttribute('data-previous-index') || 0;
            }
        });
        
        // Store the initial selected index for potential revert
        select.setAttribute('data-previous-index', select.selectedIndex);
        select.addEventListener('focus', function() {
            this.setAttribute('data-previous-index', this.selectedIndex);
        });
    });
    
    // Setup mountpoint select auto-save
    mountpointSelects.forEach(select => {
        // Skip if already has event listener
        if (select.hasAttribute('data-auto-save-initialized')) return;
        select.setAttribute('data-auto-save-initialized', 'true');
        
        if (!canEditStation) return; // Skip if user doesn't have edit permission
        
        select.addEventListener('change', async function() {
            const stationId = this.getAttribute('data-station-id');
            const mountpointDetails = this.value;
            
            if (!stationId) {
                console.error('Station ID not found for mountpoint select');
                return;
            }
            
            // Get current manager value from the same row
            const row = this.closest('tr');
            if (!row) {
                console.error('Could not find table row for mountpoint select');
                return;
            }
            
            const managerSelect = row.querySelector('.station-manager-select');
            const managerName = managerSelect ? managerSelect.value : '';
            
            try {
                await updateStation(stationId, managerName, mountpointDetails);
            } catch (error) {
                // Optionally revert the selection
                // this.selectedIndex = this.getAttribute('data-previous-index') || 0;
            }
        });
        
        // Store the initial selected index for potential revert
        select.setAttribute('data-previous-index', select.selectedIndex);
        select.addEventListener('focus', function() {
            this.setAttribute('data-previous-index', this.selectedIndex);
        });
    });
}

/**
 * Delete stations with undefined status
 */
function deleteUndefinedStations() {
    if (!canEditStation) {
        window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
        return;
    }

    if (!confirm('Bạn có chắc chắn muốn xóa tất cả các trạm có trạng thái "Không xác định"? Hành động này không thể hoàn tác!')) {
        return;
    }

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = stationHandlerUrl;
    form.style.display = 'none';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'delete_undefined_stations';

    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
}

function getManagerFormHTML(managerData = null) {
    const isEdit = managerData !== null;
    const action = isEdit ? 'update_manager' : 'create_manager';
    const managerId = isEdit ? managerData.id : '';
    const name = isEdit ? managerData.name : '';
    const phone = isEdit ? managerData.phone || '' : '';
    const address = isEdit ? managerData.address || '' : '';

    return `
        <form id="managerForm" method="POST" action="${managerHandlerUrl}">
            <input type="hidden" name="action" value="${action}">
            ${isEdit ? `<input type="hidden" name="manager_id" value="${managerId}">` : ''}
            <div class="form-group">
                <label for="managerName">Tên</label>
                <input type="text" class="form-control" id="managerName" name="name" value="${name}" required>
            </div>
            <div class="form-group">
                <label for="managerPhone">Điện thoại</label>
                <input type="text" class="form-control" id="managerPhone" name="phone" value="${phone}">
            </div>
            <div class="form-group">
                <label for="managerAddress">Địa chỉ</label>
                <input type="text" class="form-control" id="managerAddress" name="address" value="${address}">
            </div>
        </form>
    `;
}

function handleManagerFormSubmit() {
    const form = document.getElementById('managerForm');
    if (form) {
        // You might want to use FormData and fetch for AJAX submission
        // For now, we'll stick to traditional form submission
        form.submit();
    }
}

function openCreateManagerModal() {
    if (!canEditStation) {
        showToast('Bạn không có quyền thực hiện hành động này.', 'error');
        return;
    }
    document.getElementById('genericModalTitle').textContent = 'Thêm Người quản lý';
    document.getElementById('genericModalBody').innerHTML = getManagerFormHTML();
    const primaryButton = document.getElementById('genericModalPrimaryButton');
    primaryButton.textContent = 'Thêm';
    primaryButton.onclick = handleManagerFormSubmit;
    helpers.openModal('genericModal');
}

function openEditManagerModal(managerData) {
    if (!canEditStation) {
        showToast('Bạn không có quyền thực hiện hành động này.', 'error');
        return;
    }
    document.getElementById('genericModalTitle').textContent = 'Sửa Người quản lý';
    document.getElementById('genericModalBody').innerHTML = getManagerFormHTML(managerData);
    const primaryButton = document.getElementById('genericModalPrimaryButton');
    primaryButton.textContent = 'Lưu';
    primaryButton.onclick = handleManagerFormSubmit;
    helpers.openModal('genericModal');
}

// The closeManagerModal function is no longer needed as genericModal uses helpers.closeModal('genericModal')
// function closeManagerModal(modalId) {
//     helpers.closeModal(modalId);
// }

document.addEventListener('DOMContentLoaded', function() {
    // Bulk export: collect checked IDs into hidden input before submit
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function() {
            const selectedIds = Array.from(
                document.querySelectorAll('.rowCheckbox:checked')
            ).map(cb => cb.value);
            document.getElementById('selected_ids_for_export').value = selectedIds.join(',');
        });
    }

    if (!canEditStation) {
        // Disable "Thêm Người quản lý" button
        const addManagerButton = document.querySelector('button[onclick="openCreateManagerModal()"]');
        if (addManagerButton) {
            addManagerButton.disabled = true;
            addManagerButton.style.cursor = 'not-allowed';
            addManagerButton.title = 'Bạn không có quyền thực hiện hành động này.';
        }

        // Disable edit/delete buttons in managers table
        const managerActionButtons = document.querySelectorAll('#managersTable .actions button');
        managerActionButtons.forEach(button => {
            button.disabled = true;
            button.style.cursor = 'not-allowed';
            button.title = 'Bạn không có quyền thực hiện hành động này.';
        });

        // Disable select dropdowns and save buttons in stations table
        const stationSelects = document.querySelectorAll('#stationsTable select');
        stationSelects.forEach(select => {
            select.disabled = true;
        });
        const stationSaveButtons = document.querySelectorAll('#stationsTable button[type="button"].btn-primary');
        stationSaveButtons.forEach(button => {
            button.style.display = 'none'; // Hide save buttons
        });
    }

    // Tab switching
    const tabs = document.querySelectorAll('.custom-tabs-nav .nav-link');
    const contents = document.querySelectorAll('.tab-content');

    function activateTab(tabId) {
        tabs.forEach(b => b.classList.remove('active'));
        contents.forEach(c => c.style.display = (c.id === tabId ? '' : 'none'));
        const btn = document.querySelector(`.custom-tabs-nav .nav-link[data-tab="${tabId}"]`);
        if (btn) btn.classList.add('active');
        
        // Re-setup mountpoint auto-save when switching to mountpoint tab
        if (tabId === 'mountpoint') {
            // Use a small delay to ensure DOM is ready
            setTimeout(setupMountpointAutoSave, 100);
        }
        
        // Re-setup station auto-save when switching to station tab
        if (tabId === 'station') {
            // Use a small delay to ensure DOM is ready
            setTimeout(setupStationAutoSave, 100);
        }
    }

    // Replace simple click handler with URL update logic
    tabs.forEach(btn => btn.addEventListener('click', () => {
        activateTab(btn.dataset.tab);
        // Update URL parameter to reflect active tab
        const url = new URL(window.location);
        url.searchParams.set('active_tab', btn.dataset.tab);
        window.history.replaceState({}, '', url);
    }));

    // Activate initial tab based on URL parameters or window.activeTab
    const urlParams = new URLSearchParams(window.location.search);
    if (window.activeTab) {
        activateTab(window.activeTab);
    } else if (urlParams.has('manager_page')) {
        activateTab('manager-management-tab');
    } else if (urlParams.has('mountpoint_page')) {
        activateTab('mountpoint-management-tab');
    } else {
        activateTab('station-management-tab');
    }

    // Display toast if any
    if (window.initialToast) {
        showToast(window.initialToast.message, window.initialToast.type);
    }

    // --- Mountpoint Sync Functionality ---
    const fullSyncMountpointsBtn = document.getElementById('fullSyncMountpointsBtn');
    const fullSyncMountpointsModal = document.getElementById('fullSyncMountpointsModal');
    const fullSyncMountpointsStatus = document.getElementById('fullSyncMountpointsStatus');
    const confirmFullSyncMountpointsBtn = document.getElementById('confirmFullSyncMountpointsBtn');

    // Auto Update Locations functionality
    const autoUpdateLocationsBtn = document.getElementById('autoUpdateLocationsBtn');
    const autoUpdateLocationsModal = document.getElementById('autoUpdateLocationsModal');
    const autoUpdateLocationsStatus = document.getElementById('autoUpdateLocationsStatus');
    const confirmAutoUpdateLocationsBtn = document.getElementById('confirmAutoUpdateLocationsBtn');

    // API base path for mountpoint handlers
    const mountpointApiBasePath = basePath + '/public/handlers/station/mountpoint_index.php';

    // Auto Update Locations functionality
    if (autoUpdateLocationsBtn) {
        autoUpdateLocationsBtn.addEventListener('click', function() {
            if (!canEditStation) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            if (autoUpdateLocationsModal) {
                autoUpdateLocationsModal.style.display = 'block';
                autoUpdateLocationsStatus.style.display = 'none';
                confirmAutoUpdateLocationsBtn.disabled = false;
                confirmAutoUpdateLocationsBtn.textContent = '🗺️ Bắt đầu cập nhật tự động';
            }
        });
    }

    if (confirmAutoUpdateLocationsBtn) {
        confirmAutoUpdateLocationsBtn.addEventListener('click', async function() {
            const confirmation = confirm('⚠️ Xác nhận: Bắt đầu tự động cập nhật vị trí các Mount Point dựa trên masterStationNames?');
            if (!confirmation) return;
            
            confirmAutoUpdateLocationsBtn.disabled = true;
            confirmAutoUpdateLocationsBtn.textContent = '🔄 Đang xử lý...';
            autoUpdateLocationsStatus.style.display = 'block';
            autoUpdateLocationsStatus.innerHTML = '<p>🔄 Đang lấy dữ liệu từ API và phân tích...</p>';
            
            try {
                const result = await postJson(`${mountpointApiBasePath}`, {
                    action: 'auto_update_locations'
                });
                
                if (result.success) {
                    const data = result.data;
                    autoUpdateLocationsStatus.innerHTML = `
                        <div style="color: green;">
                            <p>✅ Cập nhật tự động thành công!</p>
                            <p>📊 Đã cập nhật: ${data.updated_count}/${data.total_processed} mountpoint</p>
                            ${data.updated_details && data.updated_details.length > 0 ? `
                                <div style="margin-top: 10px;">
                                    <strong>Chi tiết cập nhật:</strong>
                                    <ul style="max-height: 200px; overflow-y: auto; margin: 5px 0;">
                                        ${data.updated_details.map(detail => 
                                            `<li>${detail.mountpoint_id}: ${detail.station_name} (${detail.matched_code}) → ${detail.province || 'N/A'}</li>`
                                        ).join('')}
                                    </ul>
                                </div>
                            ` : ''}
                            ${data.errors && data.errors.length > 0 ? `
                                <div style="margin-top: 10px; color: orange;">
                                    <strong>Lỗi:</strong>
                                    <ul>${data.errors.map(error => `<li>${error}</li>`).join('')}</ul>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    setTimeout(() => {
                        window.helpers && window.helpers.closeModal ? window.helpers.closeModal('autoUpdateLocationsModal') : (document.getElementById('autoUpdateLocationsModal').style.display='none');
                        window.location.reload();
                    }, 3000);
                    
                    window.showToast(result.message || 'Cập nhật tự động thành công!', 'success');
                } else {
                    throw new Error(result.message || 'Cập nhật thất bại');
                }
            } catch (e) {
                console.error('Error in auto update locations:', e);
                autoUpdateLocationsStatus.innerHTML = `<div style="color: red;">❌ Lỗi: ${e.message}</div>`;
                window.showToast('Lỗi khi cập nhật tự động: ' + e.message, 'error');
            } finally {
                confirmAutoUpdateLocationsBtn.disabled = false;
                confirmAutoUpdateLocationsBtn.textContent = '🗺️ Bắt đầu cập nhật tự động';
            }
        });
    }

    // Full Sync functionality
    if (fullSyncMountpointsBtn) {
        fullSyncMountpointsBtn.addEventListener('click', function() {
            if (!canEditStation) {
                window.showToast('Bạn không có quyền thực hiện hành động này.', 'error');
                return;
            }
            if (fullSyncMountpointsModal) {
                fullSyncMountpointsModal.style.display = 'block';
                fullSyncMountpointsStatus.style.display = 'none';
                confirmFullSyncMountpointsBtn.disabled = false;
                confirmFullSyncMountpointsBtn.textContent = '🔄 Xác nhận đồng bộ hoàn toàn';
            }
        });
    }

    if (confirmFullSyncMountpointsBtn) {
        confirmFullSyncMountpointsBtn.addEventListener('click', async function() {
            const confirmation = confirm('⚠️ CẢNH BÁO CUỐI CÙNG: Thao tác này sẽ thay thế hoàn toàn dữ liệu mountpoint hiện tại. Bạn có chắc chắn?');
            if (!confirmation) return;
            
            confirmFullSyncMountpointsBtn.disabled = true;
            confirmFullSyncMountpointsBtn.textContent = '🔄 Đang đồng bộ...';
            fullSyncMountpointsStatus.style.display = 'block';
            fullSyncMountpointsStatus.innerHTML = '<p>🔄 Đang sao lưu dữ liệu hiện tại...</p>';
            
            try {
                const result = await postJson(`${mountpointApiBasePath}`, {
                    action: 'full_sync_mountpoints'
                });
                
                if (result.success) {
                    fullSyncMountpointsStatus.innerHTML = `
                        <div style="color: green;">
                            <p>✅ Đồng bộ hoàn toàn thành công!</p>
                            <p>📊 Đã cập nhật: ${result.data.inserted_count}/${result.data.total_records} mountpoint</p>
                        </div>
                    `;
                    
                    setTimeout(() => {
                        window.helpers && window.helpers.closeModal ? window.helpers.closeModal('fullSyncMountpointsModal') : (document.getElementById('fullSyncMountpointsModal').style.display='none');
                        window.location.reload();
                    }, 3000);
                    
                    window.showToast(result.message || 'Đồng bộ hoàn toàn thành công!', 'success');
                } else {
                    throw new Error(result.message || 'Đồng bộ thất bại');
                }
            } catch (e) {
                console.error('Error in full sync mountpoints:', e);
                fullSyncMountpointsStatus.innerHTML = `<div style="color: red;">❌ Lỗi: ${e.message}</div>`;
                window.showToast('Lỗi khi đồng bộ hoàn toàn: ' + e.message, 'error');
            } finally {
                confirmFullSyncMountpointsBtn.disabled = false;
                confirmFullSyncMountpointsBtn.textContent = '🔄 Xác nhận đồng bộ hoàn toàn';
            }
        });
    }

    // Helper functions from account management
    async function getJson(url) {
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        return await response.json();
    }

    async function postJson(url, data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        
        const response = await fetch(url, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        return await response.json();
    }

    // Initialize auto-save when page loads
    setupMountpointAutoSave();
    setupStationAutoSave();
});
