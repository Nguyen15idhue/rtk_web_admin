(function($){
    $(function(){
        // init TinyMCE
        tinymce.init({ selector:'#guideContent', height:400, menubar:false });

        const form = $('#frm-guide');
        const basePath = window.appConfig?.basePath || '';
        const apiUrl = basePath + '/public/actions/guide/index.php';

        const id = parseInt(form.find('input[name=id]').val(), 10);

        if (id) {
            (async () => {
                try {
                    const env = await api.getJson(`${apiUrl}?action=get_details&id=${id}`);
                    if (!env.success) throw new Error(env.message || 'Lỗi tải chi tiết hướng dẫn');
                    const d = env.data;
                    Object.keys(d).forEach(k => {
                        if (k !== 'thumbnail') form.find(`[name=${k}]`).val(d[k]);
                    });
                    form.find('[name=existing_thumbnail]').val(d.thumbnail || '');
                    tinymce.get('guideContent').setContent(d.content || '');
                } catch (err) {
                    window.showToast(err.message, 'error');
                }
            })();
        }

        form.on('submit', function(e){
            e.preventDefault();
            const action = id ? 'update' : 'create';
            const formData = new FormData(this);
            (async () => {
                try {
                    const res = await api.postForm(`${apiUrl}?action=${action}`, formData);
                    if (!res.success) throw new Error(res.message || 'Có lỗi xảy ra');
                    window.showToast('Lưu thành công', 'success');
                    window.location.href = 'guide_management.php';
                } catch (err) {
                    window.showToast(err.message, 'error');
                }
            })();
        });
    });
})(jQuery);
