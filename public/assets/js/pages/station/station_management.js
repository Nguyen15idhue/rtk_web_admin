// Select All logic is now handled by utils/bulk_actions.js

// manager modal helper functions

// Use basePath defined in the PHP view (e.g., station_management.php)
const managerHandlerUrl = basePath + '/public/handlers/station/manager_index.php';
const canEditStation = window.appConfig && window.appConfig.permissions && window.appConfig.permissions.station_management_edit;

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
    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        contents.forEach(c => c.style.display = (c.id === btn.dataset.tab ? '' : 'none'));
    }));

    // Display toast if any
    if (window.initialToast) {
        showToast(window.initialToast.message, window.initialToast.type);
    }
});
