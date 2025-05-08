<?php
// --- Bootstrap and Initialization ---
$bootstrap_data        = require_once __DIR__ . '/../../../private/includes/page_bootstrap.php';
$private_includes_path = $bootstrap_data['private_includes_path'];
$base_url              = $bootstrap_data['base_url'];
$admin_role            = $bootstrap_data['admin_role'];

// authorization check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
if ($invoiceId <= 0) {
    header('Location: invoice_review.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Hóa đơn #<?php echo $invoiceId; ?></title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<?php 
    include $private_includes_path . 'admin_header.php';
    include $private_includes_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-file-upload"></i> Upload Hóa đơn #<?php echo $invoiceId; ?></h2>
        </div>
        <div class="card-body">
            <form action="<?php echo $base_url; ?>public/actions/invoice/index.php?action=process_invoice_send"
                  method="post" enctype="multipart/form-data"
                  class="form-section">
                <input type="hidden" name="invoice_id" value="<?php echo $invoiceId; ?>">
                <div class="form-group">
                    <label for="invoice_file"><i class="fas fa-file-pdf"></i> Chọn file PDF:</label>
                    <input type="file" name="invoice_file" id="invoice_file"
                           accept="application/pdf" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Upload và Duyệt
                </button>
                <a href="invoice_review.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
<?php
include $private_includes_path . 'admin_footer.php';
?>