export function initializeTabSwitcher() {
    const reportContainer = document.querySelector('#admin-reports');
    if (reportContainer) {
        const tabs = reportContainer.querySelectorAll('.custom-tabs-nav .nav-link');
        const contents = reportContainer.querySelectorAll(':scope > .tab-content');

        function activateReportTab(tabName) {
            tabs.forEach(btn => btn.classList.toggle('active', btn.dataset.tab === tabName));
            contents.forEach(panel => {
                panel.style.display = (panel.id === tabName ? '' : 'none');
            });
        }

        const savedTab = localStorage.getItem('reportActiveTab') || 'overview';
        activateReportTab(savedTab);

        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const name = btn.dataset.tab;
                activateReportTab(name);
                localStorage.setItem('reportActiveTab', name);
            });
        });
    }
}
