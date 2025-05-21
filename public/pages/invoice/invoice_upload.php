<?php
// --- Bootstrap and Initialization ---
$bootstrap_data        = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$private_layouts_path = $bootstrap_data['private_layouts_path'];
$base_url              = $bootstrap_data['base_url'];
$admin_role            = $bootstrap_data['admin_role'];
$user_display_name     = $bootstrap_data['user_display_name'];

// --- Page Specific Variables ---
$invoiceId = (int)($_GET['invoice_id'] ?? 0); // Using null coalescing operator
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
    <title>Upload Hóa đơn #<?php echo htmlspecialchars((string)$invoiceId, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<?php 
    include $private_layouts_path . 'admin_header.php';
    include $private_layouts_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-file-upload"></i> Upload Hóa đơn #<?php echo htmlspecialchars((string)$invoiceId, ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/handlers/invoice/index.php?action=process_invoice_send"
                  method="post" enctype="multipart/form-data"
                  class="form-section">
                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars((string)$invoiceId, ENT_QUOTES, 'UTF-8'); ?>">
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
include $private_layouts_path . 'admin_footer.php';
?>