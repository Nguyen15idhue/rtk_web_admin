</div> <!-- /.content-wrapper --> <?php // This closing div might need adjustment depending on where content-wrapper is opened ?>
</div> <!-- /.dashboard-wrapper -->

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
    // Basic Sidebar Toggle
    const sidebar = document.getElementById('admin_sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('hamburger-btn');
    const body = document.body; // Get the body element

    function toggleSidebar() {
        if (sidebar) { // Check if sidebar exists
            const isOpen = sidebar.classList.toggle('open');
            body.classList.toggle('sidebar-is-open', isOpen); // Toggle class on body

            if (overlay) { // Check if overlay exists
                overlay.classList.toggle('open', isOpen);
            }
        }
        // Hamburger visibility is now handled by CSS based on body class and media query
    }

     // Close sidebar if window is resized FROM mobile TO desktop while sidebar is open
     window.addEventListener('resize', function() {
        const isMobileSize = window.innerWidth <= 1024;
        if (!isMobileSize && body.classList.contains('sidebar-is-open')) {
            // If resizing to desktop and mobile sidebar was open, close it
            // (Desktop sidebar is controlled differently, usually always visible or collapsible)
            // We only force close if the 'open' state was specifically for mobile toggle
            if (sidebar && sidebar.classList.contains('open')) { // Check if mobile state was open
                toggleSidebar(); // Use the toggle function to reset state
            }
        }
        // No need to manually handle hamburger visibility here, CSS does it
    });

    // Initial setup on load (e.g., close mobile sidebar if page loads on desktop)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth > 1024) {
            body.classList.remove('sidebar-is-open'); // Ensure mobile state class is removed on desktop load
            if(sidebar) sidebar.classList.remove('open'); // Ensure mobile 'open' class is removed
            if(overlay) overlay.classList.remove('open');
        } else {
            // If loading on mobile and sidebar has 'open' class (e.g., from server-side state), ensure body class is set
            if (sidebar && sidebar.classList.contains('open')) {
                body.classList.add('sidebar-is-open');
            } else {
                 body.classList.remove('sidebar-is-open');
            }
        }
    });


    // --- Toast Functionality ---
    function showToast(message, type = 'info', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        container.appendChild(toast);

        // Trigger reflow to enable animation
        toast.offsetHeight;

        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
            // Remove the element after the transition completes
            toast.addEventListener('transitionend', () => {
                if (toast.parentNode === container) { // Check if it hasn't been removed already
                    container.removeChild(toast);
                }
            }, { once: true });
             // Fallback removal if transitionend doesn't fire reliably
             setTimeout(() => {
                 if (toast.parentNode === container) {
                     container.removeChild(toast);
                 }
             }, duration + 600); // duration + transition time + buffer
        }, duration);
    }

</script>
<!-- Add other global scripts here if needed -->
<?php
// ...existing code for toasts & scripts...
// Close DB connection explicitly (singleton)
Database::getInstance()->close();
?>
</div>  <!-- End .dashboard-wrapper -->
</body>
</html>