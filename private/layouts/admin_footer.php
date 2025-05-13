<!-- Toast Container -->
<div id="toast-container"></div>

<?php
// Close DB connection explicitly (singleton)
Database::getInstance()->close();
?>
<?php include_once __DIR__ . '/generic_modal.php'; ?>
    <!-- Central vendor scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js cần giữ biến global Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tiếp các script nội bộ -->
    <!-- <script src="<?php echo $public_assets_path; ?>js/vendor.bundle.js"></script> -->
    <!-- <script src="<?php echo $public_assets_path; ?>js/app.bundle.js"></script> -->
</body>
</html>