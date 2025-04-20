</div> <!-- /.dashboard-wrapper -->
<script>
        // Basic Sidebar Toggle for Mobile
        const sidebar = document.getElementById('admin_sidebar'); // CORRECTED ID
        const overlay = document.getElementById('sidebar-overlay');
        const hamburger = document.getElementById('hamburger-btn');

        function toggleSidebar() {
            if (sidebar && overlay) { // Add checks
                sidebar.classList.toggle('open');
                overlay.classList.toggle('open');
            }
            // Optional: Hide hamburger when sidebar is open
            if (hamburger && sidebar) { // Add checks
                 hamburger.style.visibility = sidebar.classList.contains('open') ? 'hidden' : 'visible';
            }
        }

         // Close sidebar if window is resized from mobile to desktop
         window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                if (sidebar && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('open'); // Add check
                     if (hamburger) hamburger.style.visibility = 'visible';
                }
            }
        });
    </script>
</body>
</html>