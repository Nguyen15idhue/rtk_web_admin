(function($){
    $(function(){
        const form = $('#frm-guide');
        const titleInput = form.find('input[name=title]');
        const slugInput = form.find('input[name=slug]');
        const basePath = form.data('base-path') || window.basePath || '';
        const apiUrl = basePath + '/public/handlers/guide/index.php';
        const isViewMode = form.data('view-mode') === 'true' || form.data('view-mode') === true;

        const id = parseInt(form.find('input[name=id]').val(), 10);

        // Auto-save state management
        const autoSaveState = {
            timer: null,
            hasUnsavedChanges: false,
            lastSavedData: '',
            isProcessing: false,
            debounceDelay: 60000, // 60 seconds
            lastSaveTime: null,
            retryCount: 0,
            maxRetries: 3,
            elements: {
                spinner: document.getElementById('autoSaveSpinner'),
                success: document.getElementById('autoSaveSuccess'),
                error: document.getElementById('autoSaveError'),
                text: document.getElementById('autoSaveText'),
                badge: null, // Will be created dynamically
                progressBar: null // Will be created dynamically
            }
        };

        // Removed redundant verifyDOMElements function - createAutoSaveUI handles verification

        // Create auto-save UI elements
        function createAutoSaveUI() {
            if (isViewMode) return;

            // Find or create container for auto-save status
            let container = document.getElementById('autoSaveContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'autoSaveContainer';
                container.className = 'auto-save-container position-fixed';
                container.style.cssText = `
                    top: 20px;
                    right: 20px;
                    z-index: 1050;
                    min-width: 280px;
                    max-width: 320px;
                `;
                document.body.appendChild(container);
            }

            // Create auto-save status card
            container.innerHTML = `
                <div id="autoSaveCard" class="card shadow-sm border-0" style="background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); display: none;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="auto-save-icon me-2 flex-shrink-0">
                                    <i id="autoSaveSpinner" class="fas fa-spinner fa-spin" style="display: none; color: #0d6efd;"></i>
                                    <i id="autoSaveSuccess" class="fas fa-check-circle" style="display: none; color: #198754;"></i>
                                    <i id="autoSaveError" class="fas fa-exclamation-triangle" style="display: none; color: #fd7e14;"></i>
                                    <i id="autoSaveIdle" class="fas fa-clock" style="display: none; color: #6c757d;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div id="autoSaveText" class="small fw-medium text-dark" style="font-size: 0.8rem; line-height: 1.3;">
                                        Tự động lưu nháp sau 60 giây không tương tác
                                    </div>
                                    <div id="autoSaveTimestamp" class="small text-muted" style="font-size: 0.7rem; display: none; line-height: 1.2;">
                                    </div>
                                </div>
                            </div>
                            <div id="autoSaveBadge" class="badge bg-secondary flex-shrink-0 ms-2" style="display: none; font-size: 0.65rem; white-space: nowrap;">
                                Chưa lưu
                            </div>
                        </div>
                        <div id="autoSaveProgress" class="progress mt-2" style="height: 2px; display: none;">
                            <div class="progress-bar bg-primary" style="width: 0%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                </div>
            `;

            // Update element references
            autoSaveState.elements.spinner = document.getElementById('autoSaveSpinner');
            autoSaveState.elements.success = document.getElementById('autoSaveSuccess');
            autoSaveState.elements.error = document.getElementById('autoSaveError');
            autoSaveState.elements.idle = document.getElementById('autoSaveIdle');
            autoSaveState.elements.text = document.getElementById('autoSaveText');
            autoSaveState.elements.badge = document.getElementById('autoSaveBadge');
            autoSaveState.elements.progressBar = document.getElementById('autoSaveProgress');
            autoSaveState.elements.timestamp = document.getElementById('autoSaveTimestamp');
            autoSaveState.elements.card = document.getElementById('autoSaveCard');
        }

        // Update auto-save status
        function updateAutoSaveStatus(status, message, options = {}) {
            if (isViewMode) return;

            const { 
                spinner, success, error, idle, text, badge, timestamp, card, progressBar 
            } = autoSaveState.elements;
            
            // Ensure UI elements exist
            if (!card) {
                createAutoSaveUI();
                return updateAutoSaveStatus(status, message, options);
            }

            // Show the auto-save card with smooth animation
            if (card.style.display === 'none') {
                card.style.display = 'block';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-10px)';
                
                requestAnimationFrame(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }

            // Batch DOM updates
            requestAnimationFrame(() => {
                // Hide all status icons first
                [spinner, success, error, idle].forEach(el => {
                    if (el && el.style) el.style.display = 'none';
                });

                // Show appropriate icon and update badge
                const statusConfig = {
                    'saving': {
                        icon: spinner,
                        badgeText: 'Lưu...',
                        badgeClass: 'bg-primary text-white',
                        showProgress: true
                    },
                    'success': {
                        icon: success,
                        badgeText: 'Đã lưu',
                        badgeClass: 'bg-success text-white',
                        showProgress: false
                    },
                    'error': {
                        icon: error,
                        badgeText: 'Lỗi',
                        badgeClass: 'bg-warning text-dark',
                        showProgress: false
                    },
                    'idle': {
                        icon: idle,
                        badgeText: 'Chưa lưu',
                        badgeClass: 'bg-secondary text-white',
                        showProgress: false
                    }
                };

                const config = statusConfig[status] || statusConfig['idle'];
                
                // Update icon
                if (config.icon && config.icon.style) {
                    config.icon.style.display = 'inline-block';
                }

                // Update text with enhanced styling
                if (text && text.textContent !== undefined) {
                    text.textContent = message;
                    text.className = `small fw-medium ${status === 'error' ? 'text-danger' : 'text-dark'}`;
                    text.style.fontSize = '0.8rem';
                    text.style.lineHeight = '1.3';
                }

                // Update badge with better sizing
                if (badge) {
                    badge.textContent = config.badgeText;
                    badge.className = `badge ${config.badgeClass} flex-shrink-0 ms-2`;
                    badge.style.display = 'inline-block';
                    badge.style.fontSize = '0.65rem';
                    badge.style.whiteSpace = 'nowrap';
                    badge.style.minWidth = '50px';
                    badge.style.textAlign = 'center';
                }

                // Handle progress bar
                if (progressBar) {
                    if (config.showProgress) {
                        progressBar.style.display = 'block';
                        const progressBarInner = progressBar.querySelector('.progress-bar');
                        if (progressBarInner) {
                            // Animate progress bar
                            progressBarInner.style.width = '0%';
                            setTimeout(() => {
                                progressBarInner.style.width = '100%';
                            }, 100);
                        }
                    } else {
                        setTimeout(() => {
                            progressBar.style.display = 'none';
                        }, 300);
                    }
                }

                // Update timestamp for successful saves
                if (status === 'success' && timestamp) {
                    autoSaveState.lastSaveTime = new Date();
                    timestamp.textContent = `Lưu lúc ${autoSaveState.lastSaveTime.toLocaleTimeString('vi-VN')}`;
                    timestamp.style.display = 'block';
                } else if (timestamp && status !== 'success') {
                    timestamp.style.display = 'none';
                }

                // Show toast for important status changes
                if (options.showToast && window.showToast) {
                    const toastType = status === 'success' ? 'success' : status === 'error' ? 'error' : 'info';
                    window.showToast(message, toastType);
                }

                // Auto-hide after success (but keep timestamp visible)
                if (status === 'success') {
                    setTimeout(() => {
                        if (card && !autoSaveState.hasUnsavedChanges) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0.7';
                            card.style.transform = 'translateY(-5px)';
                            
                            setTimeout(() => {
                                if (!autoSaveState.hasUnsavedChanges) {
                                    card.style.display = 'none';
                                }
                            }, 3000);
                        }
                    }, 2000);
                }
            });
        }

        // Auto-save timer management
        function resetAutoSaveTimer() {
            if (isViewMode || autoSaveState.isProcessing) return;

            clearTimeout(autoSaveState.timer);
            
            // Show pending changes indicator
            if (autoSaveState.hasUnsavedChanges) {
                updateAutoSaveStatus('idle', 'Có thay đổi chưa lưu - sẽ tự động lưu sau 60 giây');
            }
            
            autoSaveState.timer = setTimeout(() => {
                if (autoSaveState.hasUnsavedChanges && !autoSaveState.isProcessing) {
                    performAutoSave();
                }
            }, autoSaveState.debounceDelay);
        }

        // Form data serialization
        function getCurrentFormData() {
            const formElements = form.find('input, select, textarea');
            const formData = new Map();
            
            formElements.each(function() {
                const $el = $(this);
                const name = $el.attr('name');
                if (name && name !== 'thumbnail') {
                    formData.set(name, $el.val());
                }
            });
            
            // Get content from TinyMCE - with null check
            const editor = tinymce.get('guideContent');
            if (editor && !editor.destroyed) {
                formData.set('content', editor.getContent());
            }
            
            // Convert Map to object and stringify
            return JSON.stringify(Object.fromEntries(formData));
        }

        // Change detection
        function checkForChanges() {
            if (isViewMode) return;

            const currentData = getCurrentFormData();
            const hasChanges = currentData !== autoSaveState.lastSavedData;
            
            if (hasChanges && !autoSaveState.hasUnsavedChanges) {
                autoSaveState.hasUnsavedChanges = true;
                autoSaveState.retryCount = 0; // Reset retry count on new changes
                resetAutoSaveTimer();
                
                // Update UI to show unsaved changes
                updateAutoSaveStatus('idle', 'Có thay đổi chưa lưu');
                
            } else if (!hasChanges && autoSaveState.hasUnsavedChanges) {
                autoSaveState.hasUnsavedChanges = false;
                clearTimeout(autoSaveState.timer);
                
                // Update UI to show no changes
                if (autoSaveState.lastSaveTime) {
                    updateAutoSaveStatus('success', 'Tất cả thay đổi đã được lưu');
                } else {
                    updateAutoSaveStatus('idle', 'Không có thay đổi');
                }
            }
        }

        // Auto-save with error handling and retry logic
        async function performAutoSave() {
            if (isViewMode || !autoSaveState.hasUnsavedChanges || autoSaveState.isProcessing) return;

            autoSaveState.isProcessing = true;
            updateAutoSaveStatus('saving', 'Đang lưu nháp...');

            try {
                // Sync TinyMCE content to ensure accuracy
                const editor = tinymce.get('guideContent');
                if (editor && !editor.destroyed) {
                    editor.save();
                }
                
                // Prepare data payload
                const formElements = form.serializeArray();
                const jsonData = formElements.reduce((acc, element) => {
                    // Skip status field for auto-save to avoid overriding user's choice
                    if (element.name !== 'status') {
                        acc[element.name] = element.value;
                    }
                    return acc;
                }, {});
                
                // Ensure content is current from TinyMCE
                if (editor && !editor.destroyed) {
                    jsonData.content = editor.getContent();
                }
                
                jsonData.is_auto_save = true;
                
                // Fetch with timeout and retry
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout
                
                const response = await fetch(`${window.basePath}public/handlers/guide/index.php?action=auto_save_guide`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData),
                    signal: controller.signal
                });
                
                clearTimeout(timeoutId);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    throw new Error('Server returned invalid JSON response');
                }
                
                if (result.success) {
                    // Handle new guide creation
                    if (!id && result.guide_id) {
                        form.find('input[name=id]').val(result.guide_id);
                        
                        // Update URL without page reload
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('id', result.guide_id);
                        window.history.replaceState({}, '', newUrl);
                    }
                    
                    // Update state
                    autoSaveState.lastSavedData = getCurrentFormData();
                    autoSaveState.hasUnsavedChanges = false;
                    autoSaveState.retryCount = 0; // Reset retry count on success
                    
                    // Success feedback
                    updateAutoSaveStatus('success', 'Nháp đã được lưu thành công', { showToast: false });
                    
                    // Save backup to localStorage
                    await saveToLocalStorage(result.guide_id);
                    
                } else {
                    throw new Error(result.message || 'Auto-save failed');
                }
                
            } catch (error) {
                autoSaveState.retryCount++;
                
                let errorMessage = 'Lỗi lưu tự động';
                if (error.name === 'AbortError') {
                    errorMessage = 'Timeout - kiểm tra kết nối mạng';
                } else if (error.message) {
                    errorMessage += ': ' + error.message;
                }
                
                // Error feedback with retry info
                if (autoSaveState.retryCount < autoSaveState.maxRetries) {
                    errorMessage += ` (Sẽ thử lại ${autoSaveState.maxRetries - autoSaveState.retryCount} lần nữa)`;
                }
                
                updateAutoSaveStatus('error', errorMessage, { 
                    showToast: autoSaveState.retryCount >= autoSaveState.maxRetries 
                });
                
                // Fallback to localStorage
                try {
                    await saveToLocalStorage();
                } catch (localError) {
                    // Silent fallback failure
                }
                
                // Schedule retry with exponential backoff
                if (autoSaveState.retryCount < autoSaveState.maxRetries) {
                    const retryDelay = Math.min(10000 * Math.pow(2, autoSaveState.retryCount - 1), 60000);
                    setTimeout(() => {
                        if (autoSaveState.hasUnsavedChanges) {
                            performAutoSave();
                        }
                    }, retryDelay);
                } else {
                    // Max retries reached, show final error
                    updateAutoSaveStatus('error', 'Không thể lưu tự động - vui lòng lưu thủ công', { 
                        showToast: true 
                    });
                }
                
            } finally {
                autoSaveState.isProcessing = false;
            }
        }

        // localStorage operations
        async function saveToLocalStorage(guideId = null) {
            try {
                const currentData = getCurrentFormData();
                const draftKey = (id || guideId) ? `guide_draft_${id || guideId}` : 'new_guide_draft';
                
                const draftData = {
                    data: JSON.parse(currentData),
                    timestamp: Date.now(),
                    id: id || guideId || null,
                    version: '1.0'
                };
                
                localStorage.setItem(draftKey, JSON.stringify(draftData));
            } catch (error) {
                // Silent localStorage failure
            }
        }

        // Draft loading with validation
        function loadDraftFromStorage() {
            if (isViewMode) return;

            try {
                const draftKey = id ? `guide_draft_${id}` : 'new_guide_draft';
                const draftStr = localStorage.getItem(draftKey);
                
                if (!draftStr) return;
                
                const draftData = JSON.parse(draftStr);
                const draftAge = Date.now() - draftData.timestamp;
                const maxAge = 24 * 60 * 60 * 1000; // 24 hours
                
                // Validate draft structure and age
                if (!draftData.data || !draftData.timestamp || draftAge > maxAge) {
                    localStorage.removeItem(draftKey);
                    return;
                }
                
                const shouldRestore = confirm(
                    'Có bản nháp chưa hoàn thành từ ' + 
                    new Date(draftData.timestamp).toLocaleString('vi-VN') + 
                    '. Bạn có muốn khôi phục không?'
                );
                
                if (shouldRestore) {
                    restoreDraftData(draftData.data);
                    updateAutoSaveStatus('success', 'Đã khôi phục bản nháp', { showToast: true });
                    
                    setTimeout(() => {
                        autoSaveState.lastSavedData = getCurrentFormData();
                        updateAutoSaveStatus('idle', 'Bản nháp đã được khôi phục');
                    }, 2000);
                } else {
                    localStorage.removeItem(draftKey);
                }
                
            } catch (error) {
                // Clean up corrupted data
                try {
                    const draftKey = id ? `guide_draft_${id}` : 'new_guide_draft';
                    localStorage.removeItem(draftKey);
                } catch (cleanupError) {
                    // Silent cleanup failure
                }
            }
        }

        // Safe draft restoration
        function restoreDraftData(data) {
            // Restore form fields efficiently
            Object.entries(data).forEach(([key, value]) => {
                if (key !== 'content') {
                    const element = form.find(`[name="${key}"]`);
                    if (element.length) {
                        element.val(value);
                    }
                }
            });
            
            // Restore TinyMCE content safely
            if (data.content) {
                safeSetContent(data.content);
            }
        }

        // Cleanup function
        function clearDraftFromStorage() {
            try {
                const draftKey = id ? `guide_draft_${id}` : 'new_guide_draft';
                localStorage.removeItem(draftKey);
            } catch (error) {
                // Silent clear failure
            }
        }

        const thumbnailInput = document.getElementById('thumbnailInput');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        const removeThumbnailBtn = document.getElementById('removeThumbnail');
        let currentThumbnailUrl = '';

        function updateThumbnailPreview(imageSrc) {
            if (!thumbnailPreview) return; // Safety check
            
            if (imageSrc) {
                thumbnailPreview.innerHTML = `
                    <img src="${imageSrc}" alt="Thumbnail preview">
                `;
                thumbnailPreview.classList.add('has-image');
                
                // Show overlay for edit mode
                if (!isViewMode) {
                    const overlay = document.getElementById('thumbnailOverlay');
                    if (overlay && overlay.style) {
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
                if (overlay && overlay.style) {
                    overlay.style.display = 'none';
                }
            }
        }

        window.removeThumbnailPreview = function() {
            if (thumbnailInput) {
                thumbnailInput.value = '';
            }
            currentThumbnailUrl = '';
            updateThumbnailPreview('');
        };

        // Handle file input change
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

        // TinyMCE State Management
        const editorState = {
            isInitialized: false,
            isReady: false,
            contentLoaded: false,
            pendingContent: null
        };

        // Safe TinyMCE content setter
        function safeSetContent(content) {
            const editor = tinymce.get('guideContent');
            if (!editor || editor.destroyed || !editorState.isReady) {
                editorState.pendingContent = content;
                return false;
            }
            
            try {
                if (content !== null && content !== undefined) {
                    editor.setContent(content);
                    editorState.contentLoaded = true;
                    editorState.pendingContent = null;
                    return true;
                }
            } catch (error) {
                console.warn('Failed to set TinyMCE content:', error);
                editorState.pendingContent = content;
                return false;
            }
            return false;
        }

        // TinyMCE initialization
        tinymce.init({ 
            selector: '#guideContent', 
            height: 500, 
            menubar: isViewMode ? false : 'edit view insert format tools table help',
            readonly: isViewMode,
            
            // Optimized toolbar - only essential tools loaded
            toolbar: isViewMode ? false : [
                'undo redo | formatselect fontselect fontsizeselect',
                'bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | outdent indent',
                'bullist numlist | blockquote hr | table | link anchor',
                'image media | code fullscreen | callout steps'
            ].join(' | '),
            
            // Essential plugins only - reduced bundle size
            plugins: isViewMode ? [] : [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'code', 'fullscreen', 'table',
                'wordcount', 'autoresize', 'codesample'
            ],
            
            // Optimized image upload configuration
            images_upload_url: `${window.basePath}public/handlers/guide/index.php?action=upload_image`,
            images_upload_credentials: true,
            images_reuse_filename: false,
            automatic_uploads: true,
            images_upload_base_path: `${window.basePath}public/uploads/guide/content/`,

            // Paste settings
            paste_data_images: true,
            paste_as_text: false,
            paste_auto_cleanup_on_paste: true,
            paste_webkit_styles: "color font-size font-weight text-decoration text-align",
            paste_merge_formats: true,
            paste_remove_styles_if_webkit: false,
            
            // Optimized content formatting
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Quote=blockquote; Code=pre',
            font_formats: 'System=-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; Arial=arial,helvetica,sans-serif; Times=times new roman,times,serif; Courier=courier new,courier,monospace',
            fontsize_formats: "12px 14px 16px 18px 20px 24px 32px",
            
            // Table settings with Bootstrap classes
            table_default_attributes: { 'class': 'table table-bordered' },
            table_default_styles: { 'border-collapse': 'collapse', 'width': '100%' },
            table_class_list: [
                {title: 'Mặc định', value: 'table table-bordered'},
                {title: 'Có viền', value: 'table table-bordered table-striped'},
                {title: 'Compact', value: 'table table-sm table-bordered'}
            ],
            
            // Link settings
            link_default_target: '_blank',
            link_class_list: [
                {title: 'Link thường', value: ''},
                {title: 'Button Primary', value: 'btn btn-primary btn-sm'},
                {title: 'Button Secondary', value: 'btn btn-secondary btn-sm'}
            ],
            
            // Code sample settings - essential languages only
            codesample_languages: [
                {text: 'HTML/XML', value: 'markup'},
                {text: 'JavaScript', value: 'javascript'},
                {text: 'CSS', value: 'css'},
                {text: 'PHP', value: 'php'},
                {text: 'JSON', value: 'json'},
                {text: 'SQL', value: 'sql'}
            ],
            
            // Performance optimizations
            statusbar: !isViewMode,
            wordcount: !isViewMode,
            branding: false,
            resize: !isViewMode,
            elementpath: !isViewMode,
            
            // Auto-resize with limits
            autoresize_min_height: 400,
            autoresize_max_height: 1000,
            autoresize_overflow_padding: 50,
            
            // Optimized content CSS - single file load
            content_css: `${window.basePath}public/assets/css/pages/guide/tinymce-content.css`,
            content_style: `
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                    font-size: 14px; 
                    line-height: 1.6; 
                    color: #333; 
                    max-width: none;
                    padding: 20px;
                    background: #fff;
                }
                h1, h2, h3, h4, h5, h6 { 
                    color: #2c3e50; 
                    margin: 1.5em 0 0.5em 0; 
                    font-weight: 600;
                    line-height: 1.3;
                }
                h1 { font-size: 2em; }
                h2 { font-size: 1.5em; }
                h3 { font-size: 1.25em; }
                p { margin: 0 0 1em 0; }
                ul, ol { margin: 0 0 1em 0; padding-left: 2em; }
                blockquote { 
                    border-left: 4px solid #3498db; 
                    margin: 1.5em 0; 
                    padding: 1em 1.5em; 
                    background: #f8f9fa; 
                    font-style: italic;
                    border-radius: 0 4px 4px 0;
                }
                img { 
                    max-width: 100%; 
                    height: auto; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                    margin: 1em 0;
                    display: block;
                }
                .btn {
                    display: inline-block;
                    padding: 6px 12px;
                    margin: 2px;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: 500;
                    text-align: center;
                    cursor: pointer;
                    border: none;
                    font-size: 14px;
                }
                .btn-primary { background-color: #007bff; color: white; }
                .btn-secondary { background-color: #6c757d; color: white; }
                .btn-sm { padding: 4px 8px; font-size: 12px; }
            `,
            
            // Image upload handler
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function(resolve, reject) {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    
                    // Progress tracking
                    xhr.upload.addEventListener('progress', function (e) {
                        if (progress && e.lengthComputable) {
                            progress(Math.round(e.loaded / e.total * 100));
                        }
                    });
                    
                    // Success handler
                    xhr.addEventListener('load', function() {
                        if (xhr.status === 403) {
                            reject({
                                message: 'Không có quyền upload ảnh',
                                remove: true
                            });
                            return;
                        }
                        
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject({
                                message: 'HTTP Error: ' + xhr.status,
                                remove: false
                            });
                            return;
                        }
                        
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject({
                                    message: 'Invalid response from server',
                                    remove: false
                                });
                                return;
                            }
                            resolve(json.location);
                        } catch (e) {
                            reject({
                                message: 'Invalid JSON response: ' + e.message,
                                remove: false
                            });
                        }
                    });
                    
                    // Error handler
                    xhr.addEventListener('error', function () {
                        reject({
                            message: 'Upload failed due to network error',
                            remove: false
                        });
                    });
                    
                    // Timeout handler
                    xhr.timeout = 30000; // 30 seconds
                    xhr.addEventListener('timeout', function () {
                        reject({
                            message: 'Upload timeout - please try again',
                            remove: false
                        });
                    });
                    
                    // Prepare and send data
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    
                    xhr.open('POST', `${window.basePath}public/handlers/guide/index.php?action=upload_image`);
                    xhr.send(formData);
                });
            },
            
            setup: function(editor) {
                editorState.isInitialized = true;
                
                if (!isViewMode) {
                    // Optimized content change tracking
                    let editorChangeTimeout;
                    editor.on('keyup change paste', function() {
                        clearTimeout(editorChangeTimeout);
                        editorChangeTimeout = setTimeout(checkForChanges, 300);
                    });
                    
                    // Custom toolbar buttons
                    editor.ui.registry.addButton('callout', {
                        text: 'Callout',
                        tooltip: 'Thêm hộp thông tin quan trọng (Ctrl+Shift+A)',
                        icon: 'info',
                        onAction: function () {
                            editor.execCommand('mceCallout');
                        }
                    });
                    
                    editor.ui.registry.addButton('steps', {
                        text: 'Steps',
                        tooltip: 'Thêm danh sách các bước thực hiện (Ctrl+Shift+P)',
                        icon: 'ordered-list',
                        onAction: function () {
                            editor.execCommand('mceSteps');
                        }
                    });

                    // Register custom commands
                    editor.addCommand('mceCallout', function() {
                        editor.insertContent(`
                            <div class="alert alert-info callout" role="alert">
                                <strong><i class="fas fa-info-circle"></i> Thông tin:</strong> Nội dung quan trọng ở đây
                            </div>
                        `);
                    });

                    editor.addCommand('mceSteps', function() {
                        editor.insertContent(`
                            <div class="steps-container">
                                <div class="step-item">
                                    <span class="step-number">1</span>
                                    <div class="step-content">
                                        <strong>Bước đầu tiên</strong><br>
                                        Mô tả chi tiết bước này
                                    </div>
                                </div>
                                <div class="step-item">
                                    <span class="step-number">2</span>
                                    <div class="step-content">
                                        <strong>Bước thứ hai</strong><br>
                                        Mô tả chi tiết bước này
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
                
                // Editor ready callback - single point of content loading
                editor.on('init', function() {
                    editorState.isReady = true;
                    
                    // Set pending content if any
                    if (editorState.pendingContent !== null) {
                        safeSetContent(editorState.pendingContent);
                    }
                    
                    // Load guide content or draft - but not both
                    if (id) {
                        loadGuideContent();
                    } else if (!isViewMode && !editorState.contentLoaded) {
                        // Only load draft for new guides and if no content is loaded yet
                        setTimeout(() => {
                            if (!editorState.contentLoaded) {
                                loadDraftFromStorage();
                            }
                        }, 500);
                    }
                });
            }
        });

        // Enhanced keyboard shortcuts for guide editor - Optimized for TinyMCE integration
        function setupKeyboardShortcuts() {
            if (isViewMode) return;

            // Global keyboard handler for non-editor shortcuts
            document.addEventListener('keydown', function(e) {
                // Enhanced typing detection - check if we're in a text input (excluding TinyMCE)
                const isTypingInInput = document.activeElement && (
                    (document.activeElement.tagName === 'INPUT' && document.activeElement.type === 'text') ||
                    (document.activeElement.tagName === 'TEXTAREA' && document.activeElement.id !== 'guideContent')
                );

                // Check if we're in TinyMCE editor
                const editor = tinymce.get('guideContent');
                const isInEditor = editor && !editor.destroyed && editor.hasFocus();

                // Global shortcuts that work anywhere
                // Ctrl+S: Quick save (override browser default)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    form.trigger('submit');
                    return;
                }

                // Ctrl+Shift+S: Auto-save now
                if (e.ctrlKey && e.shiftKey && e.key === 'S') {
                    e.preventDefault();
                    if (!autoSaveState.isProcessing) {
                        autoSaveState.hasUnsavedChanges = true;
                        performAutoSave();
                    }
                    return;
                }

                // Ctrl+P: Preview
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    const previewBtn = document.getElementById('previewBtn');
                    if (previewBtn) previewBtn.click();
                    return;
                }

                // F1: Show keyboard shortcuts help
                if (e.key === 'F1') {
                    e.preventDefault();
                    showKeyboardShortcutsHelp();
                    return;
                }

                // F11: Toggle fullscreen editor
                if (e.key === 'F11') {
                    e.preventDefault();
                    if (editor && !editor.destroyed) {
                        editor.execCommand('mceFullScreen');
                    }
                    return;
                }

                // Ctrl+Enter: Quick save (submit form)
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    form.trigger('submit');
                    return;
                }

                // Escape: Exit fullscreen or close modals
                if (e.key === 'Escape') {
                    if (editor && !editor.destroyed && editor.plugins.fullscreen && editor.plugins.fullscreen.isFullscreen()) {
                        editor.execCommand('mceFullScreen');
                        return;
                    }
                    
                    // Close any open modals
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(openModal);
                        if (bootstrapModal) {
                            bootstrapModal.hide();
                        }
                    }
                    return;
                }

                // Navigation shortcuts when not typing in inputs
                if (!isTypingInInput && !isInEditor) {
                    // Ctrl+E: Focus content editor
                    if (e.ctrlKey && e.key === 'e') {
                        e.preventDefault();
                        if (editor && !editor.destroyed) {
                            editor.focus();
                        }
                        return;
                    }

                    // Ctrl+T: Focus title field
                    if (e.ctrlKey && e.key === 't') {
                        e.preventDefault();
                        titleInput.focus().select();
                        return;
                    }

                    // Ctrl+Shift+T: Focus topic field
                    if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                        e.preventDefault();
                        const topicInput = form.find('input[name=topic]');
                        if (topicInput.length) {
                            topicInput.focus().select();
                        }
                        return;
                    }
                }

                // Upload shortcuts (work globally)
                // Ctrl+Shift+I: Upload thumbnail
                if (e.ctrlKey && e.shiftKey && e.key === 'I') {
                    e.preventDefault();
                    if (thumbnailInput) {
                        thumbnailInput.click();
                    }
                    return;
                }

                // Draft management shortcuts
                // Ctrl+Shift+R: Reload draft
                if (e.ctrlKey && e.shiftKey && e.key === 'R') {
                    e.preventDefault();
                    if (confirm('Bạn có muốn tải lại bản nháp từ localStorage không?')) {
                        loadDraftFromStorage();
                    }
                    return;
                }

                // Ctrl+Shift+D: Clear draft
                // Ctrl+Shift+D: Clear draft
                if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                    e.preventDefault();
                    if (confirm('Bạn có muốn xóa bản nháp đã lưu không?')) {
                        clearDraftFromStorage();
                        window.showToast('Đã xóa bản nháp', 'success');
                    }
                    return;
                }

                // Ctrl+Shift+E: Export content to text file
                if (e.ctrlKey && e.shiftKey && e.key === 'E') {
                    e.preventDefault();
                    exportContentAsText();
                    return;
                }
            });

            // TinyMCE-specific keyboard shortcuts setup
            // This ensures shortcuts work properly inside the editor
            function setupTinyMCEShortcuts() {
                const editor = tinymce.get('guideContent');
                if (!editor || editor.destroyed) {
                    // Retry after 500ms if editor is not ready
                    setTimeout(setupTinyMCEShortcuts, 500);
                    return;
                }

                // Add custom shortcuts directly to TinyMCE
                editor.addShortcut('ctrl+u', 'Upload image', function() {
                    editor.execCommand('mceImage');
                });

                editor.addShortcut('ctrl+shift+l', 'Insert bulleted list', function() {
                    editor.execCommand('InsertUnorderedList');
                });

                editor.addShortcut('ctrl+alt+n', 'Insert numbered list', function() {
                    editor.execCommand('InsertOrderedList');
                });

                editor.addShortcut('ctrl+k', 'Insert link', function() {
                    editor.execCommand('mceLink');
                });

                editor.addShortcut('ctrl+shift+k', 'Remove link', function() {
                    editor.execCommand('unlink');
                });

                editor.addShortcut('ctrl+shift+c', 'Insert code block', function() {
                    editor.execCommand('CodeSample');
                });

                editor.addShortcut('ctrl+shift+q', 'Insert blockquote', function() {
                    editor.execCommand('FormatBlock', false, 'blockquote');
                });

                editor.addShortcut('ctrl+shift+b', 'Insert table', function() {
                    editor.execCommand('mceInsertTable', false, {rows: 3, cols: 3});
                });

                editor.addShortcut('ctrl+shift+a', 'Insert callout', function() {
                    editor.execCommand('mceCallout');
                });

                editor.addShortcut('ctrl+shift+p', 'Insert steps', function() {
                    editor.execCommand('mceSteps');
                });

                editor.addShortcut('ctrl+shift+m', 'Highlight text', function() {
                    const selection = editor.selection.getContent();
                    if (selection) {
                        editor.insertContent(`<mark>${selection}</mark>`);
                    } else {
                        editor.insertContent('<mark>Text được tô sáng</mark>');
                    }
                });

                editor.addShortcut('ctrl+d', 'Duplicate selection', function() {
                    const selection = editor.selection.getContent();
                    if (selection) {
                        editor.insertContent('<br>' + selection);
                    }
                });

                editor.addShortcut('ctrl+/', 'Toggle comment', function() {
                    const selection = editor.selection.getContent();
                    if (selection) {
                        if (selection.includes('<!--')) {
                            // Remove comments
                            const uncommented = selection.replace(/<!--\s*|\s*-->/g, '');
                            editor.selection.setContent(uncommented);
                        } else {
                            // Add comments
                            editor.selection.setContent(`<!-- ${selection} -->`);
                        }
                    }
                });

                editor.addShortcut('ctrl+l', 'Select all', function() {
                    editor.execCommand('SelectAll');
                });

                // Alt+1-6 for headings
                for (let i = 1; i <= 6; i++) {
                    editor.addShortcut(`alt+${i}`, `Heading ${i}`, function() {
                        editor.execCommand('FormatBlock', false, `h${i}`);
                    });
                }

                // Alt+P for paragraph
                editor.addShortcut('alt+p', 'Paragraph', function() {
                    editor.execCommand('FormatBlock', false, 'p');
                });
            }

            // Setup TinyMCE shortcuts when editor is ready
            if (tinymce.get('guideContent')) {
                setupTinyMCEShortcuts();
            } else {
                // Wait for TinyMCE to initialize
                const checkEditor = setInterval(() => {
                    if (tinymce.get('guideContent')) {
                        clearInterval(checkEditor);
                        setupTinyMCEShortcuts();
                    }
                }, 100);
                
                // Clear interval after 10 seconds to prevent infinite checking
                setTimeout(() => clearInterval(checkEditor), 10000);
            }
        }

        // Show keyboard shortcuts help modal
        function showKeyboardShortcutsHelp() {
            const helpModal = document.getElementById('keyboardShortcutsModal');
            if (!helpModal) {
                createKeyboardShortcutsModal();
            }
            const modal = new bootstrap.Modal(document.getElementById('keyboardShortcutsModal'));
            modal.show();
        }

        // Make showKeyboardShortcutsHelp globally accessible
        window.showKeyboardShortcutsHelp = showKeyboardShortcutsHelp;

        // Create keyboard shortcuts help modal
        function createKeyboardShortcutsModal() {
            const modalHtml = `
                <div class="modal fade" id="keyboardShortcutsModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="margin-top: 20px !important; margin-bottom: 20px !important; max-height: calc(100vh - 40px) !important;">
                        <div class="modal-content" style="max-height: calc(100vh - 40px) !important;">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-keyboard"></i> Phím Tắt Guide Editor
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="max-height: calc(100vh - 140px) !important; overflow-y: auto !important; padding: 1.5rem !important;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">🔄 Thao Tác Cơ Bản</h6>
                                        <table class="table table-sm">
                                            <tr><td><code>Ctrl + S</code></td><td>Lưu bài viết</td></tr>
                                            <tr><td><code>Ctrl + Enter</code></td><td>Lưu nhanh</td></tr>
                                            <tr><td><code>Ctrl + Shift + S</code></td><td>Lưu nháp ngay</td></tr>
                                            <tr><td><code>Ctrl + P</code></td><td>Xem trước</td></tr>
                                            <tr><td><code>Ctrl + Z</code></td><td>Hoàn tác</td></tr>
                                            <tr><td><code>Ctrl + Y</code></td><td>Làm lại</td></tr>
                                            <tr><td><code>F11</code></td><td>Toàn màn hình</td></tr>
                                            <tr><td><code>F1</code></td><td>Hiển thị trợ giúp</td></tr>
                                            <tr><td><code>Escape</code></td><td>Thoát/Đóng</td></tr>
                                        </table>

                                        <h6 class="text-primary mt-3">🎯 Di Chuyển Focus</h6>
                                        <table class="table table-sm">
                                            <tr><td><code>Ctrl + T</code></td><td>Focus tiêu đề</td></tr>
                                            <tr><td><code>Ctrl + E</code></td><td>Focus nội dung</td></tr>
                                            <tr><td><code>Ctrl + Shift + T</code></td><td>Focus chủ đề</td></tr>
                                        </table>

                                        <h6 class="text-primary mt-3">💾 Quản Lý Nháp</h6>
                                        <table class="table table-sm">
                                            <tr><td><code>Ctrl + Shift + R</code></td><td>Tải lại nháp</td></tr>
                                            <tr><td><code>Ctrl + Shift + D</code></td><td>Xóa nháp</td></tr>
                                            <tr><td><code>Ctrl + Shift + E</code></td><td>Xuất file text</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">✏️ Định Dạng Text</h6>
                                        <table class="table table-sm">
                                            <tr><td><code>Alt + 1-6</code></td><td>Tiêu đề H1-H6</td></tr>
                                            <tr><td><code>Alt + P</code></td><td>Đoạn văn thường</td></tr>
                                            <tr><td><code>Ctrl + B</code></td><td>In đậm</td></tr>
                                            <tr><td><code>Ctrl + I</code></td><td>In nghiêng</td></tr>
                                            <tr><td><code>Ctrl + U</code></td><td>Gạch chân</td></tr>
                                            <tr><td><code>Ctrl + Shift + M</code></td><td>Tô sáng text</td></tr>
                                            <tr><td><code>Ctrl + /</code></td><td>Comment/Uncomment</td></tr>
                                        </table>

                                        <h6 class="text-primary mt-3">📝 Chèn Nội Dung</h6>
                                        <table class="table table-sm">
                                            <tr><td><code>Ctrl + K</code></td><td>Chèn link</td></tr>
                                            <tr><td><code>Ctrl + Shift + K</code></td><td>Xóa link</td></tr>
                                            <tr><td><code>Ctrl + U</code></td><td>Upload ảnh</td></tr>
                                            <tr><td><code>Ctrl + Shift + I</code></td><td>Upload thumbnail</td></tr>
                                            <tr><td><code>Ctrl + Shift + L</code></td><td>Danh sách bullet</td></tr>
                                            <tr><td><code>Ctrl + Alt + N</code></td><td>Danh sách số</td></tr>
                                            <tr><td><code>Ctrl + Shift + Q</code></td><td>Trích dẫn</td></tr>
                                            <tr><td><code>Ctrl + Shift + C</code></td><td>Code block</td></tr>
                                            <tr><td><code>Ctrl + Shift + B</code></td><td>Chèn bảng</td></tr>
                                            <tr><td><code>Ctrl + Shift + A</code></td><td>Chèn callout</td></tr>
                                            <tr><td><code>Ctrl + Shift + P</code></td><td>Chèn steps</td></tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Tip:</strong> Nhấn <code>F1</code> để xem lại danh sách phím tắt này.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
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
                }
            } catch (err) {
                // Silent error - topic suggestions may not be available
            }
        }
        loadTopics();

        function generateSlug(text) {
            if (!text) return '';
            return text.toString().toLowerCase().trim()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        // Auto-generate slug from title input (disabled in view mode)
        if (!isViewMode) {
            titleInput.on('input', function() {
                const titleValue = $(this).val();
                const slugValue = generateSlug(titleValue);
                slugInput.val(slugValue);
            });
        }

        // Load guide content (for existing guides)
        async function loadGuideContent() {
            if (!id || editorState.contentLoaded) return;
            
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
                    // Nếu là link Cloudinary thì dùng trực tiếp, nếu là tên file cũ thì ghép đường dẫn cũ
                    if (d.thumbnail.startsWith('http')) {
                        currentThumbnailUrl = d.thumbnail;
                    } else {
                        currentThumbnailUrl = basePath + '/public/uploads/guide/' + d.thumbnail;
                    }
                    updateThumbnailPreview(currentThumbnailUrl);
                }
                
                // Set content safely
                safeSetContent(d.content || '');
                
                // Update lastSavedData after loading
                if (!isViewMode) {
                    setTimeout(() => {
                        autoSaveState.lastSavedData = getCurrentFormData();
                    }, 500);
                }
                
                editorState.contentLoaded = true;
            } catch (err) {
                console.error('Error loading guide details:', err);
                window.showToast(err.message, 'error');
            }
        }

        // Preview functionality
        if (!isViewMode) {
            const previewBtn = document.getElementById('previewBtn');
            if (previewBtn) {
                previewBtn.addEventListener('click', function() {
                    // Get current form data
                    const title = form.find('input[name=title]').val() || 'Chưa có tiêu đề';
                    const topic = form.find('input[name=topic]').val() || 'Chưa chọn';
                    const content = tinymce.get('guideContent').getContent() || '<p>Chưa có nội dung</p>';
                    
                    // Get thumbnail if exists
                    const thumbnailPreview = document.querySelector('#thumbnailPreview img');
                    const thumbnailSrc = thumbnailPreview ? thumbnailPreview.src : '';
                    
                    // Update preview content
                    document.getElementById('previewTitle').textContent = title;
                    document.getElementById('previewTopic').textContent = topic;
                    document.getElementById('previewDate').textContent = new Date().toLocaleDateString('vi-VN');
                    document.getElementById('previewContent').innerHTML = content;
                    
                    // Handle thumbnail with null checks
                    const previewThumbnail = document.getElementById('previewThumbnail');
                    if (thumbnailSrc && thumbnailSrc !== '' && !thumbnailSrc.includes('placeholder')) {
                        if (previewThumbnail) {
                            previewThumbnail.src = thumbnailSrc;
                            if (previewThumbnail.style) {
                                previewThumbnail.style.display = 'block';
                            }
                        }
                    } else {
                        if (previewThumbnail && previewThumbnail.style) {
                            previewThumbnail.style.display = 'none';
                        }
                    }
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
                    modal.show();
                });
            }
        }

        form.on('submit', function(e){
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
                    
                    // Clear draft after successful save
                    clearDraftFromStorage();
                    
                    window.showToast('Lưu thành công', 'success');
                    window.location.href = 'guide_management.php';
                } catch (err) {
                    window.showToast(err.message, 'error');
                }
            })();
        });
        
        // Initialize auto-save system and change tracking
        if (!isViewMode) {
            // Create auto-save UI if needed
            createAutoSaveUI();
            
            // Setup keyboard shortcuts
            setupKeyboardShortcuts();
            
            // Enhanced change tracking for better performance
            let changeTrackingTimeout;
            const trackChanges = () => {
                clearTimeout(changeTrackingTimeout);
                changeTrackingTimeout = setTimeout(checkForChanges, 300);
            };

            form.find('input, select, textarea').on('input change', trackChanges);

            // Enhanced test auto-save button with safety check
            const testAutoSaveBtn = document.getElementById('testAutoSave');
            if (testAutoSaveBtn) {
                testAutoSaveBtn.addEventListener('click', function() {
                    if (!autoSaveState.isProcessing) {
                        autoSaveState.hasUnsavedChanges = true;
                        performAutoSave();
                    } else {
                        window.showToast('Auto-save đang trong quá trình xử lý', 'info');
                    }
                });
            }

            // Initialize saved data state and show initial status
            setTimeout(() => {
                autoSaveState.lastSavedData = getCurrentFormData();
                updateAutoSaveStatus('idle', 'Sẵn sàng tự động lưu nháp');
            }, 1000);

            // Enhanced page unload warning with auto-save prompt
            window.addEventListener('beforeunload', function(e) {
                if (autoSaveState.hasUnsavedChanges) {
                    // Try to save quickly before leaving
                    if (!autoSaveState.isProcessing) {
                        performAutoSave();
                    }
                    
                    const message = 'Bạn có thay đổi chưa lưu. Bạn có chắc muốn rời khỏi trang?';
                    e.preventDefault();
                    e.returnValue = message;
                    return message;
                }
            });

            // Enhanced cleanup on page hide/unload
            window.addEventListener('pagehide', function() {
                clearTimeout(autoSaveState.timer);
                autoSaveState.isProcessing = false;
                
                // Hide auto-save UI
                const card = autoSaveState.elements.card;
                if (card) {
                    card.style.display = 'none';
                }
            });

            // Add visibility change listener for better UX
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Page is hidden, try to save if there are changes
                    if (autoSaveState.hasUnsavedChanges && !autoSaveState.isProcessing) {
                        performAutoSave();
                    }
                } else {
                    // Page is visible again, check for changes
                    setTimeout(checkForChanges, 500);
                }
            });
        }

        // Export content as text file
        function exportContentAsText() {
            try {
                const title = form.find('input[name=title]').val() || 'Untitled Guide';
                const topic = form.find('input[name=topic]').val() || 'No Topic';
                const editor = tinymce.get('guideContent');
                const content = editor && !editor.destroyed ? editor.getContent({format: 'text'}) : '';
                
                const exportData = [
                    `Tiêu đề: ${title}`,
                    `Chủ đề: ${topic}`,
                    `Xuất lúc: ${new Date().toLocaleString('vi-VN')}`,
                    `${'='.repeat(50)}`,
                    '',
                    content
                ].join('\n');
                
                const blob = new Blob([exportData], { type: 'text/plain;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${generateSlug(title) || 'guide'}.txt`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                window.showToast('Đã xuất nội dung thành file text', 'success');
            } catch (error) {
                window.showToast('Lỗi khi xuất file', 'error');
            }
        }

        // Mobile/Touch Detection and Optimization
        const mobileState = {
            isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
            isTouch: 'ontouchstart' in window || navigator.maxTouchPoints > 0,
            orientation: window.innerHeight > window.innerWidth ? 'portrait' : 'landscape',
            viewportWidth: window.innerWidth,
            viewportHeight: window.innerHeight,
            keyboardVisible: false,
            lastTouchTime: 0,
            elements: {
                mobileToolbar: null,
                quickActions: null,
                floatingButton: null
            }
        };

        // Mobile viewport detection and keyboard handling
        function initMobileOptimizations() {
            if (!mobileState.isMobile && !mobileState.isTouch) return;

            // Create mobile-specific UI elements
            createMobileToolbar();
            createFloatingActionButton();
            setupTouchHandlers();
            setupKeyboardHandlers();
            optimizeTinyMCEForMobile();
            
            // Listen for orientation and viewport changes
            window.addEventListener('orientationchange', handleOrientationChange);
            window.addEventListener('resize', handleViewportChange);
        }

        // Create mobile-friendly toolbar
        function createMobileToolbar() {
            if (isViewMode) return;

            const mobileToolbar = document.createElement('div');
            mobileToolbar.id = 'mobileToolbar';
            mobileToolbar.className = 'mobile-toolbar position-fixed';
            mobileToolbar.style.cssText = `
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                border-top: 1px solid #e0e0e0;
                padding: 8px 12px;
                z-index: 1045;
                display: none;
                transition: transform 0.3s ease;
            `;

            mobileToolbar.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary mobile-action" data-action="preview">
                            <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Xem</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info mobile-action" data-action="auto-save">
                            <i class="fas fa-cloud"></i> <span class="d-none d-sm-inline">Nháp</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary mobile-action" data-action="save">
                            <i class="fas fa-save"></i> <span class="d-none d-sm-inline">Lưu</span>
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary mobile-action" data-action="fullscreen">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary mobile-action" data-action="minimize">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div id="mobileQuickActions" class="mt-2" style="display: none;">
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="underline">
                            <i class="fas fa-underline"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="link">
                            <i class="fas fa-link"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="image">
                            <i class="fas fa-image"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="list">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="h2">
                            H2
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark mobile-format" data-format="h3">
                            H3
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(mobileToolbar);
            mobileState.elements.mobileToolbar = mobileToolbar;
            mobileState.elements.quickActions = document.getElementById('mobileQuickActions');

            // Show mobile toolbar on mobile devices
            if (mobileState.isMobile) {
                mobileToolbar.style.display = 'block';
            }
        }

        // Create floating action button for quick access
        function createFloatingActionButton() {
            if (isViewMode) return;

            const floatingButton = document.createElement('div');
            floatingButton.id = 'floatingActionButton';
            floatingButton.className = 'floating-action-btn position-fixed';
            floatingButton.style.cssText = `
                bottom: 80px;
                right: 20px;
                width: 56px;
                height: 56px;
                background: #007bff;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
                cursor: pointer;
                z-index: 1040;
                transition: all 0.3s ease;
                user-select: none;
            `;

            floatingButton.innerHTML = `
                <i class="fas fa-feather-alt text-white" style="font-size: 20px;"></i>
            `;

            document.body.appendChild(floatingButton);
            mobileState.elements.floatingButton = floatingButton;

            // Show floating button on mobile when not focused on editor
            if (mobileState.isMobile) {
                floatingButton.style.display = 'flex';
            }
        }

        // Setup touch handlers for better mobile interaction
        function setupTouchHandlers() {
            let touchStartY = 0;
            let touchStartX = 0;
            let isScrolling = false;

            // Touch event handlers for the main form
            form.on('touchstart', function(e) {
                touchStartY = e.touches[0].clientY;
                touchStartX = e.touches[0].clientX;
                isScrolling = false;
                mobileState.lastTouchTime = Date.now();
            });

            form.on('touchmove', function(e) {
                if (!touchStartY) return;

                const touchY = e.touches[0].clientY;
                const touchX = e.touches[0].clientX;
                const deltaY = touchStartY - touchY;
                const deltaX = touchStartX - touchX;

                // Detect scrolling intent
                if (Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 10) {
                    isScrolling = true;
                }
            });

            // Mobile toolbar button handlers
            document.addEventListener('click', function(e) {
                const action = e.target.closest('.mobile-action');
                const format = e.target.closest('.mobile-format');
                const floating = e.target.closest('#floatingActionButton');

                if (action) {
                    e.preventDefault();
                    handleMobileAction(action.dataset.action);
                } else if (format) {
                    e.preventDefault();
                    handleMobileFormat(format.dataset.format);
                } else if (floating) {
                    e.preventDefault();
                    focusEditor();
                }
            });

            // Double-tap to focus editor
            let lastTap = 0;
            document.addEventListener('touchend', function(e) {
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTap;
                
                if (tapLength < 500 && tapLength > 0) {
                    // Double tap detected
                    const target = e.target.closest('.content-editor-wrapper, #guideContent');
                    if (target) {
                        e.preventDefault();
                        focusEditor();
                    }
                }
                lastTap = currentTime;
            });
        }

        // Handle mobile-specific actions
        function handleMobileAction(action) {
            switch (action) {
                case 'save':
                    form.trigger('submit');
                    break;
                case 'preview':
                    const previewBtn = document.getElementById('previewBtn');
                    if (previewBtn) previewBtn.click();
                    break;
                case 'auto-save':
                    if (!autoSaveState.isProcessing) {
                        autoSaveState.hasUnsavedChanges = true;
                        performAutoSave();
                    }
                    break;
                case 'fullscreen':
                    toggleMobileFullscreen();
                    break;
                case 'minimize':
                    minimizeMobileToolbar();
                    break;
            }
        }

        // Handle mobile formatting actions
        function handleMobileFormat(format) {
            const editor = tinymce.get('guideContent');
            if (!editor || editor.destroyed) return;

            switch (format) {
                case 'bold':
                    editor.execCommand('Bold');
                    break;
                case 'italic':
                    editor.execCommand('Italic');
                    break;
                case 'underline':
                    editor.execCommand('Underline');
                    break;
                case 'link':
                    editor.execCommand('mceLink');
                    break;
                case 'image':
                    editor.execCommand('mceImage');
                    break;
                case 'list':
                    editor.execCommand('InsertUnorderedList');
                    break;
                case 'h2':
                    editor.execCommand('FormatBlock', false, 'h2');
                    break;
                case 'h3':
                    editor.execCommand('FormatBlock', false, 'h3');
                    break;
            }
        }

        // Setup mobile keyboard detection
        function setupKeyboardHandlers() {
            let initialViewportHeight = window.innerHeight;

            window.addEventListener('resize', function() {
                const currentHeight = window.innerHeight;
                const heightDiff = initialViewportHeight - currentHeight;
                
                // Keyboard likely visible if height decreased significantly
                if (heightDiff > 150) {
                    mobileState.keyboardVisible = true;
                    handleKeyboardVisible();
                } else if (heightDiff < 50) {
                    mobileState.keyboardVisible = false;
                    handleKeyboardHidden();
                }
            });
        }

        // Handle keyboard visibility changes
        function handleKeyboardVisible() {
            const { mobileToolbar, quickActions } = mobileState.elements;
            
            if (mobileToolbar) {
                // Show quick actions when keyboard is visible
                if (quickActions) {
                    quickActions.style.display = 'block';
                }
                
                // Adjust toolbar position
                mobileToolbar.style.bottom = '0px';
                mobileToolbar.style.transform = 'translateY(0)';
            }

            // Adjust auto-save card position
            const autoSaveCard = document.getElementById('autoSaveCard');
            if (autoSaveCard) {
                autoSaveCard.style.top = '10px';
                autoSaveCard.style.right = '10px';
            }
        }

        function handleKeyboardHidden() {
            const { mobileToolbar, quickActions } = mobileState.elements;
            
            if (mobileToolbar) {
                // Hide quick actions when keyboard is hidden
                if (quickActions) {
                    quickActions.style.display = 'none';
                }
            }

            // Reset auto-save card position
            const autoSaveCard = document.getElementById('autoSaveCard');
            if (autoSaveCard) {
                autoSaveCard.style.top = '20px';
                autoSaveCard.style.right = '20px';
            }
        }

        // Optimize TinyMCE for mobile
        function optimizeTinyMCEForMobile() {
            // Wait for TinyMCE to initialize
            const checkEditor = setInterval(() => {
                const editor = tinymce.get('guideContent');
                if (editor && !editor.destroyed) {
                    clearInterval(checkEditor);
                    applyMobileEditorOptimizations(editor);
                }
            }, 100);

            setTimeout(() => clearInterval(checkEditor), 10000);
        }

        function applyMobileEditorOptimizations(editor) {
            if (!mobileState.isMobile) return;

            // Mobile-specific editor settings
            editor.on('init', function() {
                // Adjust editor for mobile
                const editorContainer = editor.getContainer();
                if (editorContainer) {
                    editorContainer.style.fontSize = '16px'; // Prevent zoom on iOS
                }

                // Hide desktop toolbar on mobile
                const toolbar = editorContainer.querySelector('.tox-toolbar');
                if (toolbar && mobileState.viewportWidth < 768) {
                    toolbar.style.display = 'none';
                }

                // Increase touch targets
                const buttons = editorContainer.querySelectorAll('.tox-tbtn');
                buttons.forEach(btn => {
                    btn.style.minHeight = '44px';
                    btn.style.minWidth = '44px';
                });
            });

            // Handle fullscreen events to keep mobile toolbar visible
            editor.on('FullscreenStateChanged', function(e) {
                const { mobileToolbar } = mobileState.elements;
                if (mobileToolbar) {
                    if (e.state) {
                        // Entering fullscreen - ensure toolbar stays visible
                        mobileToolbar.style.zIndex = '99999';
                        mobileToolbar.style.display = 'block';
                        mobileToolbar.style.position = 'fixed';
                        mobileToolbar.style.bottom = '0';
                        mobileToolbar.style.left = '0';
                        mobileToolbar.style.right = '0';
                        
                        // Update fullscreen button icon
                        const fullscreenBtn = mobileToolbar.querySelector('[data-action="fullscreen"] i');
                        if (fullscreenBtn) {
                            fullscreenBtn.className = 'fas fa-compress';
                        }
                    } else {
                        // Exiting fullscreen - restore normal z-index
                        mobileToolbar.style.zIndex = '1045';
                        
                        // Update fullscreen button icon
                        const fullscreenBtn = mobileToolbar.querySelector('[data-action="fullscreen"] i');
                        if (fullscreenBtn) {
                            fullscreenBtn.className = 'fas fa-expand';
                        }
                    }
                }
            });

            // Handle focus events
            editor.on('focus', function() {
                showMobileEditorTools();
                hideMobileFloatingButton();
            });

            editor.on('blur', function() {
                // Delay hiding tools to allow for quick interactions
                setTimeout(() => {
                    if (!editor.hasFocus()) {
                        hideMobileEditorTools();
                        showMobileFloatingButton();
                    }
                }, 300);
            });

            // Mobile-specific touch events
            editor.on('touchstart', function(e) {
                mobileState.lastTouchTime = Date.now();
            });

            // Prevent zoom on double-tap
            editor.on('click', function(e) {
                const timeSinceLastTouch = Date.now() - mobileState.lastTouchTime;
                if (timeSinceLastTouch < 300) {
                    e.preventDefault();
                }
            });
        }

        // Mobile toolbar visibility controls
        function showMobileEditorTools() {
            const { mobileToolbar } = mobileState.elements;
            if (mobileToolbar) {
                mobileToolbar.style.transform = 'translateY(0)';
                mobileToolbar.style.display = 'block';
                
                // Check if we're in fullscreen mode and adjust z-index accordingly
                const editor = tinymce.get('guideContent');
                if (editor && editor.plugins.fullscreen && editor.plugins.fullscreen.isFullscreen()) {
                    mobileToolbar.style.zIndex = '99999';
                } else {
                    mobileToolbar.style.zIndex = '1045';
                }
            }
        }

        function hideMobileEditorTools() {
            const { mobileToolbar } = mobileState.elements;
            if (mobileToolbar && !mobileState.keyboardVisible) {
                // Don't hide toolbar if we're in fullscreen mode
                const editor = tinymce.get('guideContent');
                if (editor && editor.plugins.fullscreen && editor.plugins.fullscreen.isFullscreen()) {
                    return; // Keep toolbar visible in fullscreen
                }
                mobileToolbar.style.transform = 'translateY(100%)';
            }
        }

        function showMobileFloatingButton() {
            const { floatingButton } = mobileState.elements;
            if ( floatingButton && mobileState.isMobile) {
                floatingButton.style.display = 'flex';
                floatingButton.style.transform = 'scale(1)';
            }
        }

        function hideMobileFloatingButton() {
            const { floatingButton } = mobileState.elements;
            if (floatingButton) {
                floatingButton.style.transform = 'scale(0)';
                setTimeout(() => {
                    floatingButton.style.display = 'none';
                }, 300);
            }
        }

        // Mobile fullscreen toggle
        function toggleMobileFullscreen() {
            const editor = tinymce.get('guideContent');
            if (editor && !editor.destroyed) {
                if (editor.plugins.fullscreen) {
                    editor.execCommand('mceFullScreen');
                    
                    // Ensure mobile toolbar stays visible in fullscreen
                    setTimeout(() => {
                        const { mobileToolbar } = mobileState.elements;
                        if (mobileToolbar) {
                            mobileToolbar.style.zIndex = '99999'; // Higher than TinyMCE fullscreen
                            mobileToolbar.style.display = 'block';
                            mobileToolbar.style.position = 'fixed';
                            mobileToolbar.style.bottom = '0';
                        }
                    }, 100);
                } else {
                    const editorContainer = editor.getContainer();
                    if (editorContainer) {
                        if (editorContainer.classList.contains('mobile-fullscreen')) {
                            exitMobileFullscreen(editorContainer);
                        } else {
                            enterMobileFullscreen(editorContainer);
                        }
                    }
                }
            }
        }

        // Unified mobile UI visibility helper
        function setMobileUIVisible(visible) {
            const { mobileToolbar, floatingButton } = mobileState.elements;
            if (mobileToolbar) mobileToolbar.style.display = visible ? 'block' : 'none';
            if (floatingButton) floatingButton.style.display = visible ? 'flex' : 'none';
        }

        // Minimize mobile toolbar
        function minimizeMobileToolbar() {
            const { mobileToolbar } = mobileState.elements;
            if (mobileToolbar) {
                mobileToolbar.style.transform = 'translateY(calc(100% - 40px))';
                if (!mobileToolbar.querySelector('.mobile-handle')) {
                    const handle = document.createElement('div');
                    handle.className = 'mobile-handle';
                    handle.style.cssText = `
                        position: absolute;
                        top: -20px;
                        left: 50%;
                        transform: translateX(-50%);
                        width: 40px;
                        height: 20px;
                        background: rgba(0, 0, 0, 0.1);
                        border-radius: 10px 10px  0 0;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    `;
                    handle.innerHTML = '<i class="fas fa-chevron-up text-muted" style="font-size: 12px;"></i>';
                    handle.addEventListener('click', () => {
                        mobileToolbar.style.transform = 'translateY(0)';
                        handle.remove();
                    });
                    mobileToolbar.appendChild(handle);
                }
            }
        }

        // Handle orientation changes
        function handleOrientationChange() {
            setTimeout(() => {
                const newOrientation = window.innerHeight > window.innerWidth ? 'portrait' : 'landscape';
                if (newOrientation !== mobileState.orientation) {
                    mobileState.orientation = newOrientation;
                    mobileState.viewportWidth = window.innerWidth;
                    mobileState.viewportHeight = window.innerHeight;
                    adjustForOrientation();
                }
            }, 100);
        }

        // Handle viewport changes
        function handleViewportChange() {
            mobileState.viewportWidth = window.innerWidth;
            mobileState.viewportHeight = window.innerHeight;
            if (mobileState.viewportWidth < 768 && !mobileState.isMobile) {
                setMobileUIVisible(true);
            } else if (mobileState.viewportWidth >= 768 && mobileState.isMobile) {
                setMobileUIVisible(false);
            }
        }

        // Adjust UI for orientation
        function adjustForOrientation() {
            const { mobileToolbar } = mobileState.elements;
            if (mobileToolbar) {
                mobileToolbar.style.padding = mobileState.orientation === 'landscape' ? '4px 8px' : '8px 12px';
            }
        }

        // Helper function to focus editor
        function focusEditor() {
            const editor = tinymce.get('guideContent');
            if (editor && !editor.destroyed) {
                editor.focus();
                
                // Scroll editor into view if needed
                const editorContainer = editor.getContainer();
                if (editorContainer) {
                    editorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }

        // Initialize mobile optimizations for all devices after all functions are defined
        initMobileOptimizations();

    }); // End of $(function(){
})(jQuery); // End of (function($){