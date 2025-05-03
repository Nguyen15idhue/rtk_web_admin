(function(window){
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    // ensure container is visible and non-overlapping, shifted to left
    container.style.cssText = 
        'position:fixed;top:20px;right:300px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;';

    function showToast(message, type = 'error', duration = 5000) {
        if (!container) return;
        const toast = document.createElement('div');
        // basic styling if no CSS loaded
        toast.style.cssText = 'pointer-events:auto;padding:8px 12px;border-radius:4px;color:#fff;font-size:14px;opacity:0.9;';
        toast.className = `toast toast--${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('fade-out');
            toast.addEventListener('transitionend', () => container.removeChild(toast));
        }, duration);
    }
    window.showToast = showToast;
})(window);
