document.addEventListener('DOMContentLoaded', function () {
    /**
     * Generic setup for a select-all checkbox and its associated row checkboxes.
     * @param {HTMLInputElement} selectAllElem - The "select all" checkbox element.
     * @param {NodeListOf<Element>} rowCheckboxes - The NodeList of row checkbox elements.
     * @param {HTMLButtonElement} [exportButton] - Optional export button element to enable/disable.
     */
    function setupBulk(selectAllElem, rowCheckboxes, exportButton) {
        // Toggle all row checkboxes when select-all changes
        selectAllElem.addEventListener('change', function () {
            const checked = selectAllElem.checked;
            rowCheckboxes.forEach(cb => cb.checked = checked);
            if (exportButton) exportButton.disabled = !checked;
        });

        // Update select-all and export button when any row checkbox changes
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const allChecked = Array.from(rowCheckboxes).every(i => i.checked);
                selectAllElem.checked = allChecked;
                if (exportButton) {
                    const anyChecked = Array.from(rowCheckboxes).some(i => i.checked);
                    exportButton.disabled = !anyChecked;
                }
            });
        });
    }

    // Default group: ID 'selectAll' and class 'rowCheckbox'
    const defaultSelectAll = document.getElementById('selectAll');
    if (defaultSelectAll) {
        const defaultRows = document.querySelectorAll('.rowCheckbox');
        setupBulk(defaultSelectAll, defaultRows);
    }

    // Generic groups: inputs with IDs starting with 'select-all-'
    document.querySelectorAll('input[type="checkbox"][id^="select-all-"]').forEach(selectAllElem => {
        const group = selectAllElem.id.replace('select-all-', '');
        // Derive singular name for row checkbox class, e.g., 'commissions' -> 'commission'
        const singular = group.endsWith('s') ? group.slice(0, -1) : group;
        const rowSelector = `.${singular}-checkbox`;
        const exportBtn = document.getElementById(`export-selected-${group}-btn`);
        const rowCheckboxes = document.querySelectorAll(rowSelector);
        setupBulk(selectAllElem, rowCheckboxes, exportBtn);
    });
});