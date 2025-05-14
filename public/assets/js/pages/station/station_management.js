// filepath: e:\Application\laragon\www\rtk_web_admin\public\assets\js\pages\station\station_management.js
// Select All logic is now handled by utils/bulk_actions.js

// manager modal helper functions

// Use basePath defined in the PHP view (e.g., station_management.php)
const managerHandlerUrl = basePath + '/public/handlers/station/manager_index.php';

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
    document.getElementById('genericModalTitle').textContent = 'Thêm Người quản lý';
    document.getElementById('genericModalBody').innerHTML = getManagerFormHTML();
    const primaryButton = document.getElementById('genericModalPrimaryButton');
    primaryButton.textContent = 'Thêm';
    primaryButton.onclick = handleManagerFormSubmit;
    helpers.openModal('genericModal');
}

function openEditManagerModal(managerData) {
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
});
