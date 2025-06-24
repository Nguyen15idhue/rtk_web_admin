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
    }    /**
     * Show transaction details modal
     * @param {number} transactionId Transaction History ID
     */
    function showTransactionDetails(transactionId) {
        if (!transactionId || transactionId <= 0) {
            window.showToast('ID giao dịch không hợp lệ', 'error');
            return;
        }

        const modal = document.getElementById('transaction-details-modal');
        if (!modal) {
            window.showToast('Modal không tìm thấy', 'error');
            return;
        }

        // Reset modal content and show loading state
        document.getElementById('modal-title').textContent = 'Đang tải...';
        
        // Clear previous content
        const modalBody = modal.querySelector('.modal-body');
        if (modalBody) {
            modalBody.style.opacity = '0.5';
        }
        
        // Show modal with smooth animation
        requestAnimationFrame(() => {
            modal.classList.add('active');
        });

        // Fetch transaction details
        window.api.getJson(`../../handlers/purchase/index.php?action=get_transaction_details&id=${transactionId}`)
            .then(data => {
                if (data.success && data.data) {
                    populateTransactionModal(data.data);
                    if (modalBody) {
                        modalBody.style.opacity = '1';
                    }
                } else {
                    window.showToast(data.message || 'Không thể lấy thông tin giao dịch', 'error');
                    closeTransactionDetailsModal();
                }
            })
            .catch(err => {
                console.error('Error fetching transaction details:', err);
                window.showToast('Lỗi khi tải thông tin giao dịch', 'error');
                closeTransactionDetailsModal();
            });
    }

    /**
     * Populate transaction details modal with data
     * @param {Object} data Transaction details data
     */
    function populateTransactionModal(data) {
        document.getElementById('modal-title').textContent = `Chi Tiết Giao Dịch #${data.transaction_id}`;
        document.getElementById('modal-tx-id').textContent = data.transaction_id || '';
        document.getElementById('modal-tx-email').textContent = data.user_email || '';
        document.getElementById('modal-tx-package').textContent = data.package_name || '';
        document.getElementById('modal-tx-amount').textContent = data.formatted_amount || '';
        document.getElementById('modal-tx-request-date').textContent = data.formatted_request_date || '';

        // Handle voucher information
        const voucherEl = document.getElementById('modal-tx-voucher-code');
        if (data.voucher_code) {
            let detailsText = "";
            if (data.voucher_type === 'percentage_discount') {
                detailsText = `Giảm ${data.discount_value}%`;
            } else if (data.voucher_type === 'fixed_discount') {
                detailsText = `Giảm ${window.helpers.formatCurrency(data.discount_value)}`;
            } else if (data.voucher_type === 'extend_duration') {
                detailsText = `Tặng ${data.discount_value} ngày`;
            }
            
            if (detailsText) {
                voucherEl.textContent = `${data.voucher_code} (${detailsText})`;
            } else {
                voucherEl.textContent = data.voucher_code;
            }
        } else {
            voucherEl.textContent = 'Không có';
        }

        // Populate other voucher details
        document.getElementById('modal-tx-voucher-description').textContent = data.voucher_description || '-';
        document.getElementById('modal-tx-voucher-start-date').textContent = data.voucher_start_date || '-';
        document.getElementById('modal-tx-voucher-end-date').textContent = data.voucher_end_date || '-';        // Handle status
        const statusBadge = document.getElementById('modal-tx-status-badge');
        const statusText = document.getElementById('modal-tx-status-text');
        
        // Map status to appropriate class
        let statusClass = '';
        let statusTextDisplay = '';
        
        if (data.status === 'pending') {
            statusClass = 'badge-yellow';
            statusTextDisplay = 'Đang chờ';
        } else if (data.status === 'completed' || data.status === 'active') {
            statusClass = 'badge-green';
            statusTextDisplay = 'Thành công';
        } else if (data.status === 'failed' || data.status === 'rejected') {
            statusClass = 'badge-red';
            statusTextDisplay = 'Bị từ chối';
        } else {
            statusClass = data.status_class || 'badge-gray';
            statusTextDisplay = data.status_text || data.status || '';
        }
        
        statusBadge.className = `status-badge status-badge-modal ${statusClass}`;
        statusText.textContent = statusTextDisplay;

        // Handle rejection reason
        const rejectionContainer = document.getElementById('modal-tx-rejection-reason-container');
        if (data.rejection_reason) {
            document.getElementById('modal-tx-rejection-reason').textContent = data.rejection_reason;
            rejectionContainer.style.display = 'flex';
        } else {
            rejectionContainer.style.display = 'none';
        }

        // Handle proof link
        const proofLink = document.getElementById('modal-tx-proof-link');
        if (data.payment_image) {
            const imageUrl = `${window.appConfig.baseUrl}/public/uploads/payment_proofs/${data.payment_image}`;
            proofLink.innerHTML = `<a href="${imageUrl}" target="_blank">Xem hình ảnh</a>`;
        } else {
            proofLink.textContent = 'Không có';
        }
    }

    /**
     * Close transaction details modal
     */
    function closeTransactionDetailsModal() {
        const modal = document.getElementById('transaction-details-modal');
        if (modal) {
            modal.classList.remove('active');
        }
    }    // Expose functions globally for inline onclick handlers
    window.InvoiceReviewPageEvents = {
        rejectInvoice,
        approveInvoice,
        undoInvoice,
        showTransactionDetails,
        closeTransactionDetailsModal
    };    // Initialize when dependencies are available
    waitForDependencies().then(() => {
        console.log('Invoice Review Page initialized with all dependencies');
        
        // Initialize modal event listeners
        const modal = document.getElementById('transaction-details-modal');
        if (modal) {
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeTransactionDetailsModal();
                }
            });
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('transaction-details-modal');
                if (modal && modal.classList.contains('active')) {
                    closeTransactionDetailsModal();
                }
            }
        });
        
        // Initialize any additional functionality here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Invoice Review Page DOM ready');
        });
    });

})(window);
