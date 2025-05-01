document.addEventListener('DOMContentLoaded', function() {
    const cfg = window.dashboardData || {};
    // --- New Registrations Chart ---
    const newCtx = document.getElementById('newRegistrationsChart');
    if (newCtx && cfg.newRegistrations && cfg.newRegistrations.labels.length) {
        new Chart(newCtx, {
            type: 'line',
            data: {
                labels: cfg.newRegistrations.labels,
                datasets: [{
                    label: 'Đăng ký mới',
                    data: cfg.newRegistrations.data,
                    borderColor: 'rgb(59,130,246)',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    tension: 0.1, fill: true,
                    pointBackgroundColor: 'rgb(59,130,246)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(59,130,246)'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks:{ precision:0 }, grid:{ color:'rgba(229,231,235,0.5)' } },
                    x: { grid:{ display:false } }
                },
                plugins: {
                    legend:{ display:false },
                    tooltip:{
                        mode:'index', intersect:false,
                        backgroundColor:'rgba(17,24,39,0.8)',
                        titleFont:{ weight:'bold' }, bodyFont:{ size:12 },
                        padding:10, cornerRadius:4, displayColors:false
                    }
                },
                hover:{ mode:'nearest', intersect:true }
            }
        });
    } else if (newCtx) {
        newCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu biểu đồ.</p>';
    }

    // --- Referral Chart ---
    const refCtx = document.getElementById('referralChart');
    if (refCtx && cfg.referral && cfg.referral.labels.length) {
        new Chart(refCtx, {
            type: 'line',
            data: {
                labels: cfg.referral.labels,
                datasets: [{
                    label: 'Giới thiệu HĐ',
                    data: cfg.referral.data,
                    borderColor: 'rgb(16,185,129)',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    tension: 0.1, fill: true,
                    pointBackgroundColor: 'rgb(16,185,129)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(16,185,129)'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'rgba(229,231,235,0.5)' } },
                    x: { grid:{ display:false } }
                },
                plugins: {
                    legend:{ display:false },
                    tooltip:{
                        mode:'index', intersect:false,
                        backgroundColor:'rgba(17,24,39,0.8)',
                        titleFont:{ weight:'bold' }, bodyFont:{ size:12 },
                        padding:10, cornerRadius:4, displayColors:false
                    }
                },
                hover:{ mode:'nearest', intersect:true }
            }
        });
    } else if (refCtx) {
        refCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu biểu đồ.</p>';
    }
});
