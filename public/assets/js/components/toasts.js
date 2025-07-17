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
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'toast-close';
        closeBtn.innerHTML = '×';
        closeBtn.setAttribute('aria-label', 'Close');
        
        // Create message content
        const messageEl = document.createElement('span');
        messageEl.textContent = message;
        
        toast.appendChild(messageEl);
        toast.appendChild(closeBtn);
        container.appendChild(toast);
        
        // Auto remove function
        const removeToast = () => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => {
                if (container.contains(toast)) {
                    container.removeChild(toast);
                }
            }, { once: true });
        };
        
        // Close button click handler
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            removeToast();
        });
        
        // Click to dismiss (optional)
        toast.addEventListener('click', removeToast);
        
        // trigger CSS animation in
        requestAnimationFrame(() => toast.classList.add('show'));
        
        // Auto dismiss after duration
        if (duration > 0) {
            setTimeout(removeToast, duration);
        }
    }
    window.showToast = showToast;
})(window);
