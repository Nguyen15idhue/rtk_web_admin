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

    const cfg = window.dashboardData || {};
    // --- Overview Chart (Đăng ký mới & Giới thiệu HĐ 7 ngày) ---
    const ovCtx = document.getElementById('overviewChart');
    if (ovCtx && cfg.newRegistrations && cfg.referral && cfg.newRegistrations.labels.length) {
        new Chart(ovCtx, {
            type: 'line',
            data: {
                labels: cfg.newRegistrations.labels,
                datasets: [
                    {
                        label: 'Đăng ký mới',
                        data: cfg.newRegistrations.data,
                        borderColor: 'rgb(59,130,246)',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        tension: 0.1, fill: true
                    },
                    {
                        label: 'Giới thiệu HĐ',
                        data: cfg.referral.data,
                        borderColor: 'rgb(16,185,129)',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        tension: 0.1, fill: true
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(229,231,235,0.5)' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { position: 'bottom' }
                }
            }
        });
    } else if (ovCtx) {
        ovCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }

    // --- User Package Distribution Chart ---
    const distCtx = document.getElementById('userPackageDistributionChart');
    if (distCtx && cfg.userDistribution && cfg.userDistribution.labels.length) {
        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: cfg.userDistribution.labels,
                datasets: [{
                    label: 'Người dùng',
                    data: cfg.userDistribution.data,
                    backgroundColor: 'rgba(59,130,246,0.5)',
                    borderColor: 'rgb(59,130,246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(229,231,235,0.5)' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    } else if (distCtx) {
        distCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }

    // --- User Package Ratio Chart ---
    const ratioCtx = document.getElementById('userPackageRatioChart');
    if (ratioCtx && cfg.userRatio) {
        new Chart(ratioCtx, {
            type: 'doughnut',
            data: {
                labels: ['Có Gói', 'Không có Gói'],
                datasets: [{
                    data: [cfg.userRatio.with_package || 0, cfg.userRatio.without_package || 0],
                    backgroundColor: ['rgb(16,185,129)', 'rgb(234,179,8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: { mode: 'index', intersect: false }
                }
            }
        });
    } else if (ratioCtx) {
        ratioCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }
});
