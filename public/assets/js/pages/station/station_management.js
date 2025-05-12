// toggle all checkboxes
document.getElementById('selectAll').addEventListener('change', function(){
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
});

// manager modal helper functions
function openCreateManagerModal() {
    document.getElementById('createManagerModal').style.display = 'block';
}

function openEditManagerModal(managerData) {
    document.getElementById('editManagerId').value = managerData.id;
    document.getElementById('editManagerName').value = managerData.name;
    document.getElementById('editManagerPhone').value = managerData.phone || '';
    document.getElementById('editManagerAddress').value = managerData.address || '';
    document.getElementById('editManagerModal').style.display = 'block';
}

function closeManagerModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
