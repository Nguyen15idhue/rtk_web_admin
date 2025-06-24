(function($){
    $(function(){        const form = $('#frm-guide');
        const titleInput = form.find('input[name=title]');
        const slugInput = form.find('input[name=slug]');
        const basePath = form.data('base-path') || window.basePath || '';
        const apiUrl = basePath + '/public/handlers/guide/index.php';
        const isViewMode = form.data('view-mode') === 'true';

        const id = parseInt(form.find('input[name=id]').val(), 10);

        // Thumbnail preview functionality
        const thumbnailInput = document.getElementById('thumbnailInput');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        const removeThumbnailBtn = document.getElementById('removeThumbnail');
        let currentThumbnailUrl = '';        function updateThumbnailPreview(imageSrc) {
            if (imageSrc) {
                thumbnailPreview.innerHTML = `
                    <img src="${imageSrc}" alt="Thumbnail preview">
                `;
                thumbnailPreview.classList.add('has-image');
                
                // Show overlay for edit mode
                if (!isViewMode) {
                    const overlay = document.getElementById('thumbnailOverlay');
                    if (overlay) {
                        overlay.style.display = 'flex';
                    }
                }
            } else {
                thumbnailPreview.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fas fa-image"></i>
                        <p class="mb-0 small">Nhấp để chọn ảnh</p>
                    </div>
                `;
                thumbnailPreview.classList.remove('has-image');
                
                // Hide overlay
                const overlay = document.getElementById('thumbnailOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
        }

        // Global function for removing thumbnail
        window.removeThumbnailPreview = function() {
            if (thumbnailInput) {
                thumbnailInput.value = '';
            }
            currentThumbnailUrl = '';
            updateThumbnailPreview('');
        };        // Handle file input change
        if (thumbnailInput && !isViewMode) {
            thumbnailInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        window.showToast('Vui lòng chọn file ảnh hợp lệ', 'error');
                        return;
                    }
                    
                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        window.showToast('Kích thước file không được vượt quá 5MB', 'error');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        updateThumbnailPreview(e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Handle remove button click using event delegation
        if (!isViewMode) {
            document.addEventListener('click', function(e) {
                if (e.target.id === 'removeThumbnail' || e.target.closest('#removeThumbnail')) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.removeThumbnailPreview();
                }
            });
        }

        // Initialize TinyMCE with readonly configuration for view mode
        tinymce.init({ 
            selector: '#guideContent', 
            height: 400, 
            menubar: false,
            readonly: isViewMode,
            toolbar: isViewMode ? false : 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
            plugins: isViewMode ? [] : 'lists advlist'
        });

        // Lấy danh sách chủ đề hiện có và đổ vào datalist
        async function loadTopics() {
            try {
                const resp = await api.getJson(`${apiUrl}?action=fetch_topics`);
                if (resp.success && Array.isArray(resp.data)) {
                    const datalist = document.getElementById('topicsList');
                    resp.data.forEach(topic => {
                        const opt = document.createElement('option');
                        opt.value = topic;
                        datalist.appendChild(opt);
                    });
                } else {
                    console.error('Lỗi khi lấy danh sách chủ đề:', resp.message);
                }
            } catch (err) {
                console.error('Không thể kết nối API chủ đề:', err);
            }
        }
        loadTopics();

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
        }        // Auto-generate slug from title input (disabled in view mode)
        if (!isViewMode) {
            titleInput.on('input', function() {
                const titleValue = $(this).val();
                const slugValue = generateSlug(titleValue);
                slugInput.val(slugValue);
            });
        }        if (id) {
            (async () => {
                try {
                    const env = await api.getJson(`${apiUrl}?action=get_guide_details&id=${id}`);
                    if (!env.success) throw new Error(env.message || 'Lỗi tải chi tiết hướng dẫn');
                    const d = env.data;
                    Object.keys(d).forEach(k => {
                        if (k !== 'thumbnail') form.find(`[name=${k}]`).val(d[k]);
                    });
                    form.find('[name=existing_thumbnail]').val(d.thumbnail || '');
                      // Update thumbnail preview
                    if (d.thumbnail) {
                        currentThumbnailUrl = basePath + '/public/uploads/guide/' + d.thumbnail;
                        updateThumbnailPreview(currentThumbnailUrl);
                    }
                    
                    tinymce.get('guideContent').setContent(d.content || '');
                } catch (err) {
                    window.showToast(err.message, 'error');
                }
            })();
        }form.on('submit', function(e){
            e.preventDefault();
            
            // Prevent form submission in view mode
            if (isViewMode) {
                return false;
            }
            
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
