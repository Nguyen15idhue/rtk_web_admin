(function(window, document){
    // Basic Sidebar Toggle
    const sidebar = document.getElementById('admin_sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('hamburger-btn');
    const body = document.body;
    const collapseBtn = document.getElementById('collapse-btn');

    function toggleSidebar() {
        if (!sidebar) return;
        const isOpen = sidebar.classList.toggle('open');
        body.classList.toggle('sidebar-is-open', isOpen);
        if (overlay) overlay.classList.toggle('open', isOpen);
    }

    function toggleCollapse() {
        body.classList.toggle('sidebar-is-collapsed');
        const collapsed = body.classList.contains('sidebar-is-collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed);
    }

    // Event listeners
    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
    if (collapseBtn) collapseBtn.addEventListener('click', toggleCollapse);

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
            // Restore collapse state
            const saved = localStorage.getItem('sidebarCollapsed') === 'true';
            if (saved) {
                body.classList.add('sidebar-is-collapsed');
            } else {
                body.classList.remove('sidebar-is-collapsed');
            }
            body.classList.remove('sidebar-is-open');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        } else if (sidebar && sidebar.classList.contains('open')) {
            body.classList.add('sidebar-is-open');
        }
    });

    // Expose for inline handlers
    window.toggleSidebar = toggleSidebar;

    // Submenu toggle with state persistence
    document.querySelectorAll('.parent-toggle').forEach(btn => {
        const key = btn.getAttribute('data-menu-key');
        const submenu = btn.parentElement.querySelector('.nav-submenu');
        const toggleIcon = btn.querySelector('.toggle-icon');
        // Restore saved submenu state
        const saved = localStorage.getItem('sidebarSubmenu_' + key) === 'true';
        if (saved) {
            submenu.classList.add('open');
            toggleIcon.classList.add('rotated');
        }
        // Toggle and save state on click
        btn.addEventListener('click', () => {
            const isOpen = submenu.classList.toggle('open');
            toggleIcon.classList.toggle('rotated', isOpen);
            localStorage.setItem('sidebarSubmenu_' + key, isOpen);
        });
    });

})(window, document);
