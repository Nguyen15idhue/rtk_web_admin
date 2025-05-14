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
            } else {
                errorHandler.showError('Lỗi: ' + (data.message || 'Không thể từ chối.'));
            }
        })
        .catch(err => {
            errorHandler.showError('Lỗi khi gửi yêu cầu từ chối: ' + err.message);
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
                location.reload();
            } else {
                alert(data.message || 'Đã có lỗi xảy ra');
            }
        })
        .catch(() => alert('Không thể kết nối tới server'));
    };
})(window);
