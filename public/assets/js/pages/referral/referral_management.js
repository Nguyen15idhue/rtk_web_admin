// filepath: public/assets/js/pages/referral_management.js
(function($) {
    const apiUrl = basePath + '/public/handlers/referral/process_withdrawal.php';

    function applyWithdrawalAction(id, action) {
        if (!id || !action) return;
        const confirmMsg = action === 'approve'
            ? 'Bạn có chắc muốn phê duyệt yêu cầu rút tiền này?'
            : 'Bạn có chắc muốn từ chối yêu cầu rút tiền này?';
        if (!confirm(confirmMsg)) return;

        api.getJson(`${apiUrl}?id=${id}&status=${action}`)
            .then(res => {
                if (res.success) {
                    window.showToast('Cập nhật thành công', 'success');
                    // Reload to reflect server‐rendered badge & action buttons
                    location.reload();
                } else {
                    window.showToast(res.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(err => window.showToast(err.message, 'error'));
    }

    $(function() {
        // Attach handlers for withdrawal approve/reject buttons
        $(document).on('click', '.btn-approve', function(event) {
            event.preventDefault(); // Prevent form submission
            const id = $(this).data('id');
            applyWithdrawalAction(id, 'approve');
        });
        $(document).on('click', '.btn-reject', function(event) {
            event.preventDefault(); // Prevent form submission
            const id = $(this).data('id');
            applyWithdrawalAction(id, 'reject');
        });
    });
})(jQuery);
