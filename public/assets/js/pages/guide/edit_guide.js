(function($){
    $(function(){
        // init TinyMCE
        tinymce.init({ selector:'#guideContent', height:400, menubar:false });

        const form = $('#frm-guide');
        const basePath = form.data('basePath');
        const apiUrl = basePath + '/public/actions/guide/index.php';

        const id = parseInt(form.find('input[name=id]').val(), 10);

        if (id) {
            $.getJSON(`${apiUrl}?action=get_details`, { id }, d => {
                Object.keys(d).forEach(k => {
                    if (k !== 'thumbnail') form.find(`[name=${k}]`).val(d[k]);
                });
                form.find('[name=existing_thumbnail]').val(d.thumbnail || '');
                tinymce.get('guideContent').setContent(d.content || '');
            });
        }

        form.on('submit', function(e){
            e.preventDefault();
            const action = id ? 'update' : 'create';
            const formData = new FormData(this);

            $.ajax({
                url: `${apiUrl}?action=${action}`,
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success(res){
                    if (res.success) {
                        window.showToast('Lưu thành công', 'success');
                        window.location.href = 'guide_management.php';
                    } else {
                        window.showToast(res.message || 'Có lỗi xảy ra', 'error');
                    }
                },
                error(jq, status, err){
                    window.showToast('Lỗi lưu hướng dẫn: ' + err, 'error');
                }
            });
        });
    });
})(jQuery);
