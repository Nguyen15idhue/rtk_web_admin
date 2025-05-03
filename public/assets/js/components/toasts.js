(function(window){
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    // ensure container is visible and non-overlapping, shifted to left
    container.classList.add('toast-container');

    function showToast(message, type = 'error', duration = 5000) {
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        // trigger CSS animation in
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            // trigger CSS animation out
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => {
                // only remove if still in container
                if (container.contains(toast)) {
                    container.removeChild(toast);
                }
            }, { once: true });
        }, duration);
    }
    window.showToast = showToast;
})(window);
