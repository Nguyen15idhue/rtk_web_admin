// filepath: public/assets/js/pages/referral_management.js
// Handles actions on the Referral Management page
(function($) {
    const apiUrl = basePath + '/public/handlers/referral/process_withdrawal.php';

    function applyWithdrawalAction(id, action) {
        if (!id || !action) return;
        // Confirm action
        const confirmMsg = action === 'approve' ? 'Bạn có chắc muốn phê duyệt yêu cầu rút tiền này?' : 'Bạn có chắc muốn từ chối yêu cầu rút tiền này?';
        if (!confirm(confirmMsg)) return;
        api.getJson(`${apiUrl}?id=${id}&status=${action}`)
            .then(res => {
                if (!res.success) throw new Error(res.message);
                alert('Cập nhật thành công');
                // Refresh the page or row
                location.reload();
            })
            .catch(err => alert(err.message));
    }

    $(function() {
        // Attach handlers for withdrawal approve/reject buttons
        $(document).on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            applyWithdrawalAction(id, 'approve');
        });
        $(document).on('click', '.btn-reject', function() {
            const id = $(this).data('id');
            applyWithdrawalAction(id, 'reject');
        });

        // --- New code for "Select All" and "Export Selected" ---

        function initializeExportCheckboxes(tabPrefix, selectAllCheckboxId, itemCheckboxClass, exportSelectedButtonId) {
            const $selectAll = $('#' + selectAllCheckboxId);
            const $itemCheckboxes = $('.' + itemCheckboxClass);
            const $exportSelectedButton = $('#' + exportSelectedButtonId);

            function updateExportButtonState() {
                const anySelected = $itemCheckboxes.is(':checked');
                $exportSelectedButton.prop('disabled', !anySelected);
            }

            $selectAll.on('change', function() {
                $itemCheckboxes.prop('checked', $(this).prop('checked'));
                updateExportButtonState();
            });

            $itemCheckboxes.on('change', function() {
                if (!$itemCheckboxes.not(':checked').length) {
                    $selectAll.prop('checked', true);
                } else {
                    $selectAll.prop('checked', false);
                }
                updateExportButtonState();
            });

            // Initial state
            updateExportButtonState();
        }

        // Bulk select-all logic now centralized in utils/bulk_actions.js
    });
})(jQuery);
