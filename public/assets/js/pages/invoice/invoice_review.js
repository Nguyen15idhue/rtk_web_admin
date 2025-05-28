/**
 * Invoice Review Page - Main Functionality
 * Handles invoice approval, rejection, and undo actions
 */
(function(window) {
    'use strict';

    // Wait for dependencies to be available
    function waitForDependencies() {
        return new Promise((resolve) => {
            const checkDependencies = () => {
                if (typeof window.api !== 'undefined' && 
                    typeof window.helpers !== 'undefined' && 
                    typeof window.showToast !== 'undefined') {
                    resolve();
                } else {
                    setTimeout(checkDependencies, 50);
                }
            };
            checkDependencies();
        });
    }

    /**
     * Reject an invoice with reason
     * @param {number} id Invoice ID
     */
    function rejectInvoice(id) {
        const reason = prompt('Nhập lý do từ chối:');
        if (!reason || !reason.trim()) {
            return;
        }        
        const trimmedReason = reason.trim();
        
        window.api.postJson('../../handlers/invoice/index.php?action=process_invoice_reject', {
            invoice_id: id,
            reason: trimmedReason
        })
        .then(data => {
            if (data.success) {
                updateInvoiceRowAfterReject(id, trimmedReason);
                window.showToast(data.message || 'Hóa đơn đã được từ chối thành công!', 'success');
            } else {
                window.showToast(data.message || 'Đã có lỗi xảy ra khi từ chối hóa đơn', 'error');
            }
        })
        .catch(err => {
            console.error('Error rejecting invoice:', err);
            window.showToast('Lỗi kết nối khi từ chối hóa đơn. Vui lòng thử lại.', 'error');
        });
    }

    /**
     * Approve an invoice
     * @param {number} id Invoice ID
     */
    function approveInvoice(id) {
        if (!confirm('Bạn có chắc muốn phê duyệt hóa đơn này?')) {
            return;        }
        
        window.api.postJson('../../handlers/invoice/index.php?action=process_invoice_approve', {
            invoice_id: id
        })
        .then(data => {
            if (data.success) {
                updateInvoiceRowAfterApprove(id);
                window.showToast(data.message || 'Hóa đơn đã được phê duyệt thành công!', 'success');
            } else {
                window.showToast(data.message || 'Đã có lỗi xảy ra khi phê duyệt hóa đơn', 'error');
            }
        })
        .catch(err => {
            console.error('Error approving invoice:', err);
            window.showToast('Lỗi kết nối khi phê duyệt hóa đơn. Vui lòng thử lại.', 'error');
        });
    }

    /**
     * Undo invoice status change
     * @param {number} invoiceId Invoice ID
     */
    function undoInvoice(invoiceId) {
        if (!confirm('Bạn có chắc muốn hoàn tác thay đổi trạng thái của hóa đơn này?')) {
            return;        }

        window.api.postJson('../../handlers/invoice/index.php?action=process_invoice_revert', {
            invoice_id: invoiceId
        })
        .then(data => {
            if (data.success) {
                window.showToast(data.message || 'Đã hoàn tác thành công!', 'success');
                
                // Redirect or reload page
                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                window.showToast(data.message || 'Đã có lỗi xảy ra khi hoàn tác', 'error');
            }
        })
        .catch(err => {
            console.error('Error undoing invoice:', err);
            window.showToast('Lỗi kết nối khi hoàn tác. Vui lòng thử lại.', 'error');
        });
    }

    /**
     * Update invoice row after rejection
     * @param {number} id Invoice ID
     * @param {string} reason Rejection reason
     */
    function updateInvoiceRowAfterReject(id, reason) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;

        // Update status column (9th column)
        const statusCell = row.querySelector('td:nth-child(9)');
        if (statusCell) {
            statusCell.innerHTML = '<span class="status-badge status-rejected">Từ chối</span>';
        }

        // Update rejected reason column (11th column)
        const reasonCell = row.querySelector('td:nth-child(11)');
        if (reasonCell) {
            reasonCell.innerHTML = `<span class="rejection-reason rejected">${reason}</span>`;
        }

        // Update actions column (12th column)
        const actionsCell = row.querySelector('td:nth-child(12)');
        if (actionsCell) {
            actionsCell.innerHTML = `
                <button class="btn-icon btn-undo"
                        onclick="InvoiceReviewPageEvents.undoInvoice(${id})"
                        title="Hoàn tác">
                    <i class="fas fa-undo"></i>
                </button>
            `;
        }
    }

    /**
     * Update invoice row after approval
     * @param {number} id Invoice ID
     */
    function updateInvoiceRowAfterApprove(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;

        // Update status column (9th column)
        const statusCell = row.querySelector('td:nth-child(9)');
        if (statusCell) {
            statusCell.innerHTML = '<span class="status-badge status-approved">Đã duyệt</span>';
        }

        // Update actions column (12th column)
        const actionsCell = row.querySelector('td:nth-child(12)');
        if (actionsCell) {
            actionsCell.innerHTML = `
                <button class="btn-icon btn-undo"
                        onclick="InvoiceReviewPageEvents.undoInvoice(${id})"
                        title="Hoàn tác">
                    <i class="fas fa-undo"></i>
                </button>
            `;
        }
    }    // Expose functions globally for inline onclick handlers
    window.InvoiceReviewPageEvents = {
        rejectInvoice,
        approveInvoice,
        undoInvoice
    };

    // Initialize when dependencies are available
    waitForDependencies().then(() => {
        console.log('Invoice Review Page initialized with all dependencies');
        
        // Initialize any additional functionality here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Invoice Review Page DOM ready');
        });
    });

})(window);
