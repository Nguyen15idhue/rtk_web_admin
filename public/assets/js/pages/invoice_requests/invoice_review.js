function rejectInvoice(id) {
    const reason = prompt('Nhập lý do từ chối:');
    if (!reason) return;
    fetch('../../actions/invoice_requests/index.php?action=process_invoice_reject', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ invoice_id: id, reason: reason.trim() })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector('tr[data-id="' + id + '"]');
            row.querySelector('td:nth-child(6)').textContent = 'Rejected';
            row.querySelector('td:nth-child(8)').textContent = reason;
            row.querySelector('td:nth-child(9)').innerHTML = '<span>Đã từ chối</span>';
        } else {
            errorHandler.showError('Lỗi: ' + (data.message || 'Không thể từ chối.'));
        }
    })
    .catch(err => {
        errorHandler.showError('Lỗi khi gửi yêu cầu từ chối: ' + (err.message||err));
    });
}
// expose for inline onclick
window.rejectInvoice = rejectInvoice;
