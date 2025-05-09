(function($){
    $(function(){
        // init TinyMCE
        tinymce.init({ selector:'#guideContent', height:400, menubar:false });

        const form = $('#frm-guide');
        const titleInput = form.find('input[name=title]');
        const slugInput = form.find('input[name=slug]');
        const basePath = window.appConfig?.basePath || '';
        const apiUrl = basePath + '/public/handlers/guide/index.php';

        const id = parseInt(form.find('input[name=id]').val(), 10);

        // Function to generate a URL-friendly slug
        function generateSlug(text) {
            if (!text) return '';
            text = text.toString().toLowerCase().trim();

            // Normalize Vietnamese characters (remove diacritics)
            text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            // Replace 'đ' with 'd'
            text = text.replace(/đ/g, 'd');

            // Replace non-alphanumeric characters (except hyphens) with a hyphen
            text = text.replace(/[^a-z0-9]+/g, '-');

            // Trim hyphens from start and end
            text = text.replace(/^-+|-+$/g, '');

            return text;
        }

        // Auto-generate slug from title input
        titleInput.on('input', function() {
            const titleValue = $(this).val();
            const slugValue = generateSlug(titleValue);
            slugInput.val(slugValue);
        });

        if (id) {
            (async () => {
                try {
                    const env = await api.getJson(`${apiUrl}?action=get_guide_details&id=${id}`);
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
            const action = id ? 'update_guide' : 'create_guide';
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
