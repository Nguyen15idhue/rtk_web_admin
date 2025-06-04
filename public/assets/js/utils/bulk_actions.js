document.addEventListener('DOMContentLoaded', function () {
    /**
     * Generic setup for a select-all checkbox and its associated row checkboxes.
     * @param {HTMLInputElement} selectAllElem - The "select all" checkbox element.
     * @param {NodeListOf<Element>} rowCheckboxes - The NodeList of row checkbox elements.
     * @param {HTMLButtonElement} [exportButton] - Optional export button element to enable/disable.
     */
    function setupBulk(selectAllElem, rowCheckboxes, exportButton) {
        let lastSelectedIndex = -1;
        const checkboxArray = Array.from(rowCheckboxes);
        
        // Toggle all row checkboxes when select-all changes
        selectAllElem.addEventListener('change', function () {
            const checked = selectAllElem.checked;
            rowCheckboxes.forEach(cb => cb.checked = checked);
            if (exportButton) exportButton.disabled = !checked;
            lastSelectedIndex = -1; // Reset selection index
        });

        // Update select-all and export button when any row checkbox changes
        checkboxArray.forEach((cb, index) => {
            cb.addEventListener('change', function () {
                const allChecked = checkboxArray.every(i => i.checked);
                selectAllElem.checked = allChecked;
                if (exportButton) {
                    const anyChecked = checkboxArray.some(i => i.checked);
                    exportButton.disabled = !anyChecked;
                }
                
                // Update last selected index if checkbox is checked
                if (cb.checked) {
                    lastSelectedIndex = index;
                }
            });
            
            // Add click handler for Shift+Click functionality
            cb.addEventListener('click', function (e) {
                if (e.shiftKey && lastSelectedIndex !== -1 && lastSelectedIndex !== index) {
                    e.preventDefault(); // Prevent default checkbox behavior
                    
                    const startIndex = Math.min(lastSelectedIndex, index);
                    const endIndex = Math.max(lastSelectedIndex, index);
                    const shouldCheck = cb.checked || checkboxArray[lastSelectedIndex].checked;
                    
                    // Select range of checkboxes
                    for (let i = startIndex; i <= endIndex; i++) {
                        checkboxArray[i].checked = shouldCheck;
                    }
                    
                    // Trigger change event to update UI
                    const changeEvent = new Event('change', { bubbles: true });
                    cb.dispatchEvent(changeEvent);
                }
            });
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Only work if focus is within the table/form area or no input is focused
            const activeElement = document.activeElement;
            const isInputFocused = activeElement && (
                activeElement.tagName === 'INPUT' || 
                activeElement.tagName === 'TEXTAREA' || 
                activeElement.contentEditable === 'true'
            );
            
            if (isInputFocused) return;
            
            if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                // Select all checkboxes
                checkboxArray.forEach(cb => cb.checked = true);
                selectAllElem.checked = true;
                if (exportButton) exportButton.disabled = false;
                
                // Trigger change event to update UI
                const changeEvent = new Event('change', { bubbles: true });
                selectAllElem.dispatchEvent(changeEvent);
            }
            
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                // Deselect all checkboxes
                checkboxArray.forEach(cb => cb.checked = false);
                selectAllElem.checked = false;
                if (exportButton) exportButton.disabled = true;
                lastSelectedIndex = -1;
                
                // Trigger change event to update UI
                const changeEvent = new Event('change', { bubbles: true });
                selectAllElem.dispatchEvent(changeEvent);
            }
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

    // Support pages like invoice_management: selectAllTx and class 'tx-checkbox'
    document.querySelectorAll('input[type="checkbox"][id^="selectAll"]')
        .forEach(selectAllElem => {
            if (selectAllElem.id === 'selectAll') return;
            const group = selectAllElem.id.replace('selectAll', '');
            const singular = group.toLowerCase();
            const rowSelector = `.${singular}-checkbox`;
            const exportBtn = document.getElementById(`export-selected-${singular}-btn`);
            const rowCheckboxes = document.querySelectorAll(rowSelector);
            setupBulk(selectAllElem, rowCheckboxes, exportBtn);
        });
    
    // Handle export_selected clicks: populate selected_ids hidden input
    document.querySelectorAll('button[name="export_selected"], button[name="export_selected_excel"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');
            const checkboxSelector = 'input[type="checkbox"]:checked:not([id^="select"]):not([id="selectAll"])';
            const checkboxes = document.querySelectorAll(checkboxSelector);
            const ids = Array.from(checkboxes).map(cb => cb.value);
            let input = form.querySelector('input[name="selected_ids"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids';
                form.appendChild(input);
            }
            input.value = ids.join(',');
            
            // If this button is export_selected (legacy), add export_selected_excel for backend detection
            if (this.name === 'export_selected') {
                let flagInput = form.querySelector('input[name="export_selected_excel"]');
                if (!flagInput) {
                    flagInput = document.createElement('input');
                    flagInput.type = 'hidden';
                    flagInput.name = 'export_selected_excel';
                    flagInput.value = this.value || '';
                    form.appendChild(flagInput);
                }
            }
        });
    });
});