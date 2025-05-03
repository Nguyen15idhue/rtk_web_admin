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
            const msg = thrown || jqxhr.statusText || 'Unknown AJAX error';
            window.showToast(`AJAX error: ${msg}`, 'error');
        });
    }
    window.errorHandler = { showError: handleError };
})(window);
