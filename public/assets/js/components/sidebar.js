(function(window, document){
    // Basic Sidebar Toggle
    const sidebar = document.getElementById('admin_sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('hamburger-btn');
    const body = document.body;

    function toggleSidebar() {
        if (!sidebar) return;
        const isOpen = sidebar.classList.toggle('open');
        body.classList.toggle('sidebar-is-open', isOpen);
        if (overlay) overlay.classList.toggle('open', isOpen);
    }

    // Event listeners
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);

    // Handle resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 1024;
        if (!isMobile && body.classList.contains('sidebar-is-open')) {
            toggleSidebar();
        }
    });

    // Initial setup
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth > 1024) {
            body.classList.remove('sidebar-is-open');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        } else if (sidebar && sidebar.classList.contains('open')) {
            body.classList.add('sidebar-is-open');
        }
    });

})(window, document);
