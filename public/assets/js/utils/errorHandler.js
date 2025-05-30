(function(window){
    function handleError(msg) {
        if (window.showToast) window.showToast(msg, 'error');
        else console.error(msg);
    }
    window.addEventListener('error', e => {
        handleError(e.message);
    });
    window.addEventListener('unhandledrejection', e => {
        handleError(e.reason?.message || 'Unhandled promise rejection');
    });
    // Bắt tất cả lỗi Ajax của jQuery
    if (window.jQuery) {
        jQuery(document).ajaxError((_, jqxhr, settings, thrown) => {
            let msg = thrown || jqxhr.statusText || 'Unknown AJAX error';
            if (jqxhr.status === 403) {
                msg = 'Bạn không có quyền thực hiện hành động này.';
            }
            window.showToast(`AJAX error: ${msg}`, 'error');
        });
    }
    window.errorHandler = { showError: handleError };
})(window);
