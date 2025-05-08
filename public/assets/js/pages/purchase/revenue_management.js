document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.rowCheckbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
        });
    }
});
