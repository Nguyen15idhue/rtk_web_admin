(function($){
    const apiUrl = basePath + '/public/handlers/guide/index.php';
    const canEditGuide = window.appConfig && window.appConfig.permissions && window.appConfig.permissions.guide_management_edit;    $(function(){
        // View button opens edit page in view-only mode
        $(document).on('click', '.btn-view', function(){
            const id = $(this).data('id');
            window.location.href = 'edit_guide.php?id=' + id + '&mode=view';
        });

        // Edit button navigates to edit page
        $(document).on('click', '.btn-edit', function(){
            if (!canEditGuide) return;
            window.location.href = 'edit_guide.php?id=' + $(this).data('id');
        });        // Toggle status button
        $(document).on('click', '.btn-toggle', function(){
            if (!canEditGuide) return;
            const $btn = $(this);
            const id = $btn.data('id');
            const st = $btn.data('status');
            $btn.prop('disabled', true);
            api.postJson(`${apiUrl}?action=toggle_guide_status`, { id, status: st })
                .then(env => {
                    if (!env.success) throw new Error(env.message || 'Toggle thất bại');
                    // Reload page to reflect updated status with pagination
                    window.location.reload();
                })
                .catch(err => {
                    window.showToast(err.message, 'error');
                    $btn.prop('disabled', false);
                });
        });

        // Delete button with confirmation
        $(document).on('click', '.btn-delete', function(){
            if (!canEditGuide) return;
            const $btn = $(this);
            const id = $btn.data('id');
            const title = $btn.data('title');
            
            if (!confirm(`Bạn có chắc chắn muốn xóa hướng dẫn "${title}"?\n\nHành động này không thể hoàn tác!`)) {
                return;
            }
            
            $btn.prop('disabled', true);
            api.postJson(`${apiUrl}?action=delete_guide`, { id })
                .then(env => {
                    if (!env.success) throw new Error(env.message || 'Xóa thất bại');
                    window.showToast('Xóa hướng dẫn thành công', 'success');
                    // Reload page to reflect changes with pagination
                    window.location.reload();
                })
                .catch(err => {
                    window.showToast(err.message, 'error');
                    $btn.prop('disabled', false);
                });
        });
    });
})(jQuery);
