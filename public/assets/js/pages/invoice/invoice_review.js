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
                row.querySelector('td:nth-child(9)').innerHTML = '<span>Đã từ chối</span>';
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
})(window);
