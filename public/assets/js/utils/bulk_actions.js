document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const bulkActionForm = document.getElementById('bulkActionForm');
    const exportSelectedButton = document.querySelector('button[name="export_selected"]');
    // const exportAllButton = document.querySelector('button[name="export_all"]'); // If you have a separate export all button

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }

    if (rowCheckboxes.length > 0) {
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    // Check if all row checkboxes are checked
                    let allChecked = true;
                    rowCheckboxes.forEach(cb => {
                        if (!cb.checked) {
                            allChecked = false;
                        }
                    });
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
    }

    if (bulkActionForm && exportSelectedButton) {
        exportSelectedButton.addEventListener('click', function(event) {
            let oneChecked = false;
            rowCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    oneChecked = true;
                }
            });

            if (!oneChecked) {
                event.preventDefault(); // Stop form submission
                alert('Vui lòng chọn ít nhất một mục để xuất.');
            } else {
                // Ensure the form action is set for exporting selected
                // This is mostly handled by the button's name or can be explicitly set if needed
                // bulkActionForm.action = 'private/actions/export_excel.php';
                // Add a hidden input to specify the action if your backend needs it
                // let actionInput = bulkActionForm.querySelector('input[name="action_type"]');
                // if (!actionInput) {
                //    actionInput = document.createElement('input');
                //    actionInput.type = 'hidden';
                //    actionInput.name = 'action_type';
                //    bulkActionForm.appendChild(actionInput);
                // }
                // actionInput.value = 'export_selected';
            }
        });
    }

    // If you have a separate "Export All" button and want to ensure no IDs are sent
    // if (bulkActionForm && exportAllButton) {
    //     exportAllButton.addEventListener('click', function(event) {
    //         // Temporarily disable checkboxes to prevent their values from being submitted
    //         rowCheckboxes.forEach(checkbox => {
    //             checkbox.disabled = true;
    //         });
    //         // Submit the form
    //         // The form will submit, and then we re-enable them in case submission fails or for SPA behavior
    //         setTimeout(() => {
    //             rowCheckboxes.forEach(checkbox => {
    //                 checkbox.disabled = false;
    //             });
    //         }, 100);
    //     });
    // }

});
