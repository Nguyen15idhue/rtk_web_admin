document.addEventListener('DOMContentLoaded', function() {
    // Đọc tab đã chọn gần nhất từ localStorage
    const savedTab = localStorage.getItem('dashboardActiveTab');
    // scope everything inside the dashboard wrapper only
    const dashboardContainer = document.querySelector('main.content-wrapper');
    const tabs = dashboardContainer.querySelectorAll('.custom-tabs-nav .nav-link');
    // only target direct children panels of this dashboard
    const contents = dashboardContainer.querySelectorAll(':scope > .tab-content');

    function activateTab(tabName) {
        tabs.forEach(b => b.classList.toggle('active', b.dataset.tab === tabName));
        contents.forEach(c => c.style.display = (c.id === tabName ? '' : 'none'));
    }
    // Nếu đã lưu và có tab tương ứng, kích hoạt; ngược lại mặc định là 'overview'
    if (savedTab && document.querySelector(`.custom-tabs-nav .nav-link[data-tab="${savedTab}"]`)) {
        activateTab(savedTab);
    } else {
        activateTab('overview');
    }
    // Thiết lập event listener và lưu khi click
    tabs.forEach(btn => btn.addEventListener('click', () => {
        const tabName = btn.dataset.tab;
        activateTab(tabName);
        localStorage.setItem('dashboardActiveTab', tabName);
    }));
});
