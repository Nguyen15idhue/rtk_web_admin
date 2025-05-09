<?php
$bootstrap_data = require_once __DIR__ . '/../../private/includes/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_includes_path = $bootstrap_data['private_includes_path'];
?>
<?php include $private_includes_path . 'admin_header.php'; ?>
<?php include $private_includes_path . 'admin_sidebar.php'; ?>
<main class="content-wrapper">
    <div class="container text-center mt-12 p-6 shadow border rounded bg-white">
        <h1 class="text-2xl font-semibold mb-4">Xin lỗi!</h1>
        <p class="text-lg">Hệ thống đang gặp sự cố. Vui lòng thử lại sau.</p>
    </div>
</main>
<?php include $private_includes_path . 'admin_footer.php'; ?>
