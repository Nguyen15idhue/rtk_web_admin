// Chart helper functions for formatting
export function formatCurrencyShort(value) {
    if (value >= 1000000) {
        return (value / 1000000).toFixed(1) + 'tr';
    } else if (value >= 1000) {
        return (value / 1000).toFixed(0) + 'k';
    }
    return new Intl.NumberFormat('vi-VN').format(value);
}

export function formatCurrencyFull(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
}

export function formatDateShort(dateStr) {
    // Input is already in dd/mm/yyyy format from PHP
    return dateStr;
}

export function formatDateForMobile(dateStr) {
    // Convert dd/mm/yyyy to shorter format dd/mm for mobile
    if (dateStr && dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length >= 2) {
            return parts[0] + '/' + parts[1];
        }
    }
    return dateStr;
}

export function isMobile() {
    return window.innerWidth <= 768;
}

export function getResponsiveChartOptions(chartType = 'line') {
    const isMobileDevice = isMobile();
    
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
        }
    };

    if (chartType === 'line' || chartType === 'bar') {
        baseOptions.scales = {
            x: {
                grid: { display: false },
                ticks: {
                    autoSkip: true,
                    maxTicksLimit: isMobileDevice ? 4 : 7,
                    maxRotation: isMobileDevice ? 45 : 0,
                    font: {
                        family: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                        size: isMobileDevice ? 10 : 12,
                        weight: '500'
                    },
                    color: '#6B7280',
                    padding: isMobileDevice ? 4 : 8,
                    callback: function(value, index, values) {
                        const label = this.getLabelForValue(value);
                        return isMobileDevice ? formatDateForMobile(label) : label;
                    }
                },
                border: {
                    display: false
                }
            }
        };
    }

    return baseOptions;
}
