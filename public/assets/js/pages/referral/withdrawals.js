// public/assets/js/pages/referral/withdrawals.js
// Handle export selected withdrawals
(function() {
    const exportBtn = document.getElementById('export-selected-withdrawals-btn');
    const form = document.getElementById('export-withdrawals-form');
    if (!exportBtn || !form) return;
    exportBtn.addEventListener('click', function(e) {
        const checkboxes = document.querySelectorAll('.withdrawal-checkbox:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn mục để xuất.');
            return;
        }
        // Remove any previous selected_ids inputs
        form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());
        // Append selected IDs as hidden inputs
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });
    });
})();
