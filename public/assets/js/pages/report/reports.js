document.addEventListener('DOMContentLoaded', function() {
    const reportFilterForm = document.getElementById('report-filter-form');
    if (reportFilterForm) {
        reportFilterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const startDate = document.getElementById('report-start-date').value;
            const endDate = document.getElementById('report-end-date').value;
            const urlParams = new URLSearchParams({ start_date: startDate, end_date: endDate });
            window.location.search = urlParams.toString();
        });
    }
});
