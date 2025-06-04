/**
 * Revenue Management Page - JavaScript functionality
 * Handles select all functionality and other interactive features
 */
(function(window) {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const appBase = (window.appConfig && window.appConfig.baseUrl) ? window.appConfig.baseUrl : '';
        
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionButtons();
            });
        }

        // Individual checkbox changes
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateBulkActionButtons();
            });
        });

        /**
         * Update the state of select all checkbox based on individual checkboxes
         */
        function updateSelectAllState() {
            if (!selectAllCheckbox) return;
            
            const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
            const totalCount = rowCheckboxes.length;
            
            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }

        /**
         * Update bulk action button states
         */
        function updateBulkActionButtons() {
            const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
            const exportSelectedBtn = document.querySelector('button[name="export_selected"]');
            
            if (exportSelectedBtn) {
                exportSelectedBtn.disabled = checkedCount === 0;
                exportSelectedBtn.textContent = checkedCount > 0 
                    ? `📊 Xuất mục đã chọn (${checkedCount})` 
                    : '📊 Xuất mục đã chọn';
            }
        }

        /**
         * Filter reset confirmation
         */
        const clearFiltersBtn = document.querySelector('.btn-secondary[href*="?"]');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function(e) {
                if (this.href.includes('?')) {
                    e.preventDefault();
                    if (confirm('Bạn có chắc muốn xóa tất cả bộ lọc?')) {
                        window.location.href = this.href;
                    }
                }
            });
        }

        /**
         * Form validation for date filters
         */
        const dateFromInput = document.querySelector('input[name="date_from"]');
        const dateToInput = document.querySelector('input[name="date_to"]');

        if (dateFromInput && dateToInput) {
            dateFromInput.addEventListener('change', validateDateRange);
            dateToInput.addEventListener('change', validateDateRange);
        }

        function validateDateRange() {
            if (dateFromInput.value && dateToInput.value) {
                const fromDate = new Date(dateFromInput.value);
                const toDate = new Date(dateToInput.value);
                
                if (fromDate > toDate) {
                    alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc');
                    dateFromInput.focus();
                    return false;
                }
                
                // Check if date range is too large (more than 1 year)
                const diffTime = Math.abs(toDate - fromDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 365) {
                    if (!confirm('Khoảng thời gian lớn hơn 1 năm có thể làm chậm quá trình tải. Bạn có muốn tiếp tục?')) {
                        return false;
                    }
                }
            }
            return true;
        }

        /**
         * Initialize tooltips for stats boxes
         */
        const statsBoxes = document.querySelectorAll('.stats-box');
        statsBoxes.forEach(box => {
            const label = box.querySelector('.label');
            if (label) {
                switch(label.textContent.trim()) {
                    case 'Tổng doanh thu':
                        box.title = 'Tổng số tiền từ tất cả giao dịch (bao gồm cả đang chờ và bị từ chối)';
                        break;
                    case 'Doanh thu thành công':
                        box.title = 'Tổng số tiền từ các giao dịch đã được duyệt thành công';
                        break;
                    case 'Doanh thu chờ duyệt':
                        box.title = 'Tổng số tiền từ các giao dịch đang chờ phê duyệt';
                        break;
                    case 'Doanh thu bị từ chối':
                        box.title = 'Tổng số tiền từ các giao dịch bị từ chối';
                        break;
                    case 'Tỷ lệ thành công':
                        box.title = 'Tỷ lệ phần trăm doanh thu thành công so với tổng doanh thu';
                        break;
                    case 'Tổng giao dịch':
                        box.title = 'Tổng số lượng giao dịch';
                        break;
                }
            }
        });

        // Initialize the page
        updateSelectAllState();
        updateBulkActionButtons();

        // Expose functions globally if needed
        window.RevenueManagement = {
            updateSelectAllState: updateSelectAllState,
            updateBulkActionButtons: updateBulkActionButtons
        };

        // Global functions for form actions
        window.exportRevenueSummary = function() {
            if (confirm('Xuất báo cáo tổng hợp doanh thu? Báo cáo sẽ bao gồm thống kê tổng quan và chi tiết theo thời gian.')) {
                console.log('Export revenue summary clicked');
                
                // Update the hidden form with current filter values
                const hiddenForm = document.getElementById('revenueSummaryForm');
                if (hiddenForm) {
                    const dateFrom = document.querySelector('input[name="date_from"]');
                    const dateTo = document.querySelector('input[name="date_to"]');
                    const status = document.querySelector('select[name="status"]');
                    const packageId = document.querySelector('select[name="package_id"]');
                    
                    if (dateFrom) {
                        hiddenForm.querySelector('input[name="date_from"]').value = dateFrom.value || '';
                    }
                    if (dateTo) {
                        hiddenForm.querySelector('input[name="date_to"]').value = dateTo.value || '';
                    }
                    if (status) {
                        hiddenForm.querySelector('input[name="status"]').value = status.value || '';
                    }
                    if (packageId) {
                        hiddenForm.querySelector('input[name="package_id"]').value = packageId.value || '';
                    }
                    
                    console.log('Submitting hidden form');
                    hiddenForm.submit();
                } else {
                    console.error('Hidden form not found');
                    alert('Lỗi: Không tìm thấy form xuất báo cáo');
                }
            }
        };
    });

})(window);