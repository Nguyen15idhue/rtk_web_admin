(function(window) {
    function rejectInvoice(id) {
        const reason = prompt('Nhập lý do từ chối:');
        if (!reason) return;
        api.postJson('../../handlers/invoice/index.php?action=process_invoice_reject', {
            invoice_id: id,
            reason: reason.trim()
        })
        .then(data => {
            if (data.success) {
                const row = document.querySelector('tr[data-id="' + id + '"]');
                row.querySelector('td:nth-child(6)').innerHTML = '<span class="status-badge status-rejected">Rejected</span>';
                row.querySelector('td:nth-child(8)').innerHTML = `<span style="color: red;">${reason.trim()}</span>`;
                row.querySelector('td:nth-child(9)').innerHTML = `
                    <button class="btn-icon btn-undo"
                            onclick="InvoiceReviewPageEvents.undoInvoice(${id})"
                            title="Hoàn tác">
                        <i class="fas fa-undo"></i>
                    </button>`;
                window.showToast(data.message || 'Thao tác thành công!', 'success');
            } else {
                window.showToast(data.message || 'Đã có lỗi xảy ra', 'error');
            }
        })
        .catch(err => {
            window.showToast('Lỗi khi gửi yêu cầu từ chối: ' + err.message, 'error');
        });
    }
    // expose for inline onclick
    window.InvoiceReviewPageEvents = {
        rejectInvoice
    };

    InvoiceReviewPageEvents.undoInvoice = function(invoiceId) {
        if (!confirm('Bạn có chắc muốn hoàn tác thay đổi trạng thái?')) return;
        api.postJson('../../handlers/invoice/index.php?action=process_invoice_revert', {
            invoice_id: invoiceId
        })
        .then(data => {
            if (data.success) {
                window.showToast(data.message || 'Thao tác thành công!', 'success');
                // Optionally, redirect or update UI further
                if (data.redirect_url) {
                    setTimeout(() => { window.location.href = data.redirect_url; }, 2000);
                } else {
                    // Fallback if no redirect URL is provided but action was successful
                    setTimeout(() => { window.location.reload(); }, 2000);
                }
            } else {
                window.showToast(data.message || 'Đã có lỗi xảy ra', 'error');
            }
        })
        .catch(() => window.showToast('Không thể kết nối tới server', 'error'));
    };
})(window);
