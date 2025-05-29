import { initializeDateRangePresets } from './modules/dateRangePresets.js';
import { initializeReportFormHandler } from './modules/reportFormHandler.js';
import { initializeTabSwitcher } from './modules/tabSwitcher.js';

document.addEventListener('DOMContentLoaded', function() {
    initializeDateRangePresets();
    initializeReportFormHandler();
    initializeTabSwitcher();

    // Chart configurations
    const cfg = window.reportChartData || {};

    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueTrendChart');
    if (revenueCtx && cfg.revenueTrend && cfg.revenueTrend.labels.length) {
        const labels = cfg.revenueTrend.labels;
        const revenueData = labels.map((_, i) => cfg.revenueTrend.data[i] || 0);
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#1D4ED8',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    borderCapStyle: 'round',
                    borderJoinStyle: 'round'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        position: 'left',
                        ticks: { 
                            precision: 0,
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 12,
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }, 
                        grid: { 
                            color: 'rgba(229, 231, 235, 0.3)',
                            lineWidth: 1,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            autoSkip: true, 
                            maxTicksLimit: 7, 
                            maxRotation: 0,
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 8
                        },
                        border: {
                            display: false
                        }
                    }
                },
                plugins: {
                    tooltip: { 
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        mode: 'index', 
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                return 'Ngày: ' + context[0].label;
                            },
                            label: function(context) {
                                return '💰 ' + context.dataset.label + ': ' + 
                                       new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                            }
                        }
                    },
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151',
                            padding: 20
                        }
                    }
                }
            }
        });
    } else if (revenueCtx) {
        revenueCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu doanh thu.</p>';
    }

    // Transaction Status Chart
    const statusCtx = document.getElementById('transactionStatusChart');
    if (statusCtx && cfg.transactionStatus && cfg.transactionStatus.data.some(val => val > 0)) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: cfg.transactionStatus.labels,
                datasets: [{
                    data: cfg.transactionStatus.data,
                    backgroundColor: [
                        '#10B981',    // Modern green for completed
                        '#F59E0B',    // Modern amber for pending  
                        '#EF4444'     // Modern red for failed
                    ],
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff',
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: { 
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151',
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length && data.datasets[0].data.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => (a || 0) + (b || 0), 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i] || 0;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        callbacks: {
                            title: function(context) {
                                return '📊 ' + context[0].label;
                            },
                            label: function(context) {
                                const label = context.label || '';
                                const count = context.parsed;
                                const amount = cfg.transactionStatus.amounts[context.dataIndex] || 0;
                                const total = cfg.transactionStatus.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                                
                                return [
                                    `💳 Số lượng: ${count} giao dịch`,
                                    `💰 Số tiền: ${new Intl.NumberFormat('vi-VN').format(amount)} đ`,
                                    `📈 Tỷ lệ: ${percentage}%`
                                ];
                            }
                        }
                    }
                }
            }
        });
    } else if (statusCtx) {
        statusCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu giao dịch.</p>';
    }

    // Overview Chart (Đăng ký mới & Giới thiệu HĐ 7 ngày)
    const ovCtx = document.getElementById('overviewChart');
    if (ovCtx && cfg.newRegistrations && cfg.referral && cfg.newRegistrations.labels.length) {
        const labels = cfg.newRegistrations.labels;
        const regData = labels.map((_, i) => cfg.newRegistrations.data[i] || 0);
        const refData = labels.map((_, i) => cfg.referral.data[i] || 0);
        new Chart(ovCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { 
                        label: 'Đăng ký mới', 
                        data: regData, 
                        borderColor: '#8B5CF6', 
                        backgroundColor: 'rgba(139, 92, 246, 0.1)', 
                        pointBackgroundColor: '#8B5CF6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#7C3AED',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3,
                        tension: 0.4, 
                        fill: true,
                        borderWidth: 3,
                        borderCapStyle: 'round'
                    },
                    { 
                        label: 'Giới thiệu HĐ', 
                        data: refData, 
                        borderColor: '#06B6D4', 
                        backgroundColor: 'rgba(6, 182, 212, 0.1)', 
                        pointBackgroundColor: '#06B6D4',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#0891B2',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3,
                        tension: 0.4, 
                        fill: true,
                        borderWidth: 3,
                        borderCapStyle: 'round'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            precision: 0,
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 12
                        }, 
                        grid: { 
                            color: 'rgba(229, 231, 235, 0.3)',
                            lineWidth: 1,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    },
                    x: { 
                        grid: { display: false }, 
                        ticks: { 
                            autoSkip: true, 
                            maxTicksLimit: 7,
                            maxRotation: 0,
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 8
                        },
                        border: {
                            display: false
                        }
                    }
                },
                plugins: {
                    tooltip: { 
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        mode: 'index', 
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                return 'Ngày: ' + context[0].label;
                            },
                            label: function(context) {
                                const icons = {
                                    'Đăng ký mới': '👥',
                                    'Giới thiệu HĐ': '🤝'
                                };
                                return icons[context.dataset.label] + ' ' + context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    },
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151',
                            padding: 20
                        }
                    }
                }
            }
        });
    } else if (ovCtx) {
        ovCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }

    // User Package Distribution Chart
    const distCtx = document.getElementById('userPackageDistributionChart');
    if (distCtx && cfg.userDistribution && cfg.userDistribution.labels.length) {
        new Chart(distCtx, {
            type: 'bar',
            data: { 
                labels: cfg.userDistribution.labels, 
                datasets: [{ 
                    label: 'Người dùng', 
                    data: cfg.userDistribution.data, 
                    backgroundColor: [
                        '#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#84CC16'
                    ].slice(0, cfg.userDistribution.labels.length),
                    borderColor: [
                        '#2563EB', '#7C3AED', '#059669', '#D97706', '#DC2626', '#0891B2', '#65A30D'
                    ].slice(0, cfg.userDistribution.labels.length),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: [
                        '#2563EB', '#7C3AED', '#059669', '#D97706', '#DC2626', '#0891B2', '#65A30D'
                    ].slice(0, cfg.userDistribution.labels.length),
                    hoverBorderWidth: 3
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                scales: { 
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 12
                        },
                        grid: {
                            color: 'rgba(229, 231, 235, 0.3)',
                            lineWidth: 1,
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    }, 
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280',
                            padding: 8
                        },
                        border: {
                            display: false
                        }
                    }
                }, 
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        callbacks: {
                            title: function(context) {
                                return '📦 ' + context[0].label;
                            },
                            label: function(context) {
                                return '👥 Người dùng: ' + context.parsed.y;
                            }
                        }
                    }
                } 
            }
        });
    } else if (distCtx) {
        distCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }

    // User Package Ratio Chart
    const ratioCtx = document.getElementById('userPackageRatioChart');
    if (ratioCtx && cfg.userRatio) {
        new Chart(ratioCtx, {
            type: 'doughnut', 
            data: { 
                labels: ['Có Gói', 'Không có Gói'], 
                datasets: [{ 
                    data: [cfg.userRatio.with_package || 0, cfg.userRatio.without_package || 0], 
                    backgroundColor: ['#10B981', '#F59E0B'],
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff',
                    cutout: '65%'
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                plugins: { 
                    legend: { 
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151',
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length && data.datasets[0].data.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => (a || 0) + (b || 0), 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i] || 0;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    }, 
                    tooltip: { 
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        mode: 'index', 
                        intersect: false,
                        callbacks: {
                            title: function(tooltipItems) {
                                if (tooltipItems.length > 0 && tooltipItems[0].label) {
                                    return '📦 Trạng thái: ' + tooltipItems[0].label;
                                }
                                return '';
                            },
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;

                                let total = 0;
                                if (context.chart && context.chart.data && context.chart.data.datasets && context.chart.data.datasets.length > 0 && context.chart.data.datasets[0].data) {
                                   total = context.chart.data.datasets[0].data.reduce((acc, val) => (acc || 0) + (val || 0), 0);
                                }
                                
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                
                                return `👥 ${label}: ${value} người dùng (${percentage}%)`;
                            }
                        }
                    } 
                } 
            }
        });
    } else if (ratioCtx) {
        ratioCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu.</p>';
    }

    // Commission Analytics Chart (Pie Chart)
    const commAnalyticsCtx = document.getElementById('commissionAnalyticsChart');
    if (commAnalyticsCtx && cfg.commissionAnalytics && cfg.commissionAnalytics.data.some(v => v > 0)) {
        new Chart(commAnalyticsCtx, {
            type: 'doughnut',
            data: {
                labels: cfg.commissionAnalytics.labels,
                datasets: [{
                    data: cfg.commissionAnalytics.data,
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'],
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff',
                    cutout: '55%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            color: '#374151',
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length && data.datasets[0].data.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => (a || 0) + (b || 0), 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i] || 0;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 16,
                        titleFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            size: 13,
                            weight: '500'
                        },
                        callbacks: {
                            title: function(context) {
                                return '💰 ' + context[0].label;
                            },
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const amount = cfg.commissionAnalytics.amounts[context.dataIndex];
                                const total = cfg.commissionAnalytics.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                const formattedAmount = new Intl.NumberFormat('vi-VN', {
                                    style: 'currency', currency: 'VND'
                                }).format(amount || 0);
                                
                                return [
                                    `📋 ${value} yêu cầu`,
                                    `💵 ${formattedAmount}`,
                                    `📊 ${percentage}%`
                                ];
                            }
                        }
                    }
                }
            }
        });
    } else if (commAnalyticsCtx) {
        commAnalyticsCtx.parentNode.innerHTML = '<p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Không có dữ liệu hoa hồng.</p>';
    }

    // Flatpickr date pickers
    const sd = document.getElementById('report-start-date');
    const ed = document.getElementById('report-end-date');
    if (sd && ed && window.flatpickr) {
        flatpickr(sd, { dateFormat: 'Y-m-d', defaultDate: window.defaultStartDate });
        flatpickr(ed, { dateFormat: 'Y-m-d', defaultDate: window.defaultEndDate });
        sd.addEventListener('change', () => ed._flatpickr.set('minDate', sd.value));
        ed.addEventListener('change', () => sd._flatpickr.set('maxDate', ed.value));
    }
});
