// public/assets/js/pages/referral/commissions.js
// Handle export selected commissions
(function() {
    const exportBtn = document.getElementById('export-selected-commissions-btn');
    const form = document.getElementById('export-commissions-form');
    if (!exportBtn || !form) return;
    exportBtn.addEventListener('click', function(e) {
        const checkboxes = document.querySelectorAll('.commission-checkbox:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn mục để xuất.');
            return;
        }
        form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });
    });
})();
