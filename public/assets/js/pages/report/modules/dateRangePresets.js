export function initializeDateRangePresets() {
    const presetButtons = document.querySelectorAll('.preset-btn');
    const startDateInput = document.getElementById('report-start-date');
    const endDateInput = document.getElementById('report-end-date');

    presetButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            presetButtons.forEach(b => b.classList.remove('btn-primary', 'active'));
            presetButtons.forEach(b => b.classList.add('btn-outline-secondary'));
            
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');
            
            const today = new Date();
            let startDate, endDate;
            
            if (this.dataset.days) {
                const days = parseInt(this.dataset.days);
                startDate = new Date(today);
                startDate.setDate(today.getDate() - days);
                endDate = new Date(today);
            } else if (this.dataset.period) {
                const period = this.dataset.period;
                
                switch(period) {
                    case 'this_quarter':
                        const quarter = Math.floor(today.getMonth() / 3);
                        startDate = new Date(today.getFullYear(), quarter * 3, 1);
                        endDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                        break;
                    case 'this_year':
                        startDate = new Date(today.getFullYear(), 0, 1);
                        endDate = new Date(today.getFullYear(), 11, 31);
                        break;
                    default:
                        startDate = new Date(today);
                        startDate.setDate(today.getDate() - 7);
                        endDate = new Date(today);
                }
            } else {
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 7);
                endDate = new Date(today);
            }
            
            if (!startDate || !endDate || isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                console.error('Invalid dates generated');
                return;
            }
            
            const formatDate = (date) => date.toISOString().split('T')[0];
            startDateInput.value = formatDate(startDate);
            endDateInput.value = formatDate(endDate);
            
            document.getElementById('report-filter-form').submit();
        });
    });
}
