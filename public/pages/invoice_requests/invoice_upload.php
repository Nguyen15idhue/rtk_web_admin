<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

require_once __DIR__ . '/../../../private/config/database.php';
require_once __DIR__ . '/../../../private/classes/Database.php';

$invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
if ($invoiceId <= 0) {
    header('Location: invoice_review.php');
    exit;
}

$private_includes_path = __DIR__ . '/../../../private/includes/';

// --- Base Path Calculation for assets (from invoice_review.php) ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off' || $_SERVER['SERVER_PORT']==443)
             ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$idx = array_search('rtk_web_admin', $parts);
$base_path = $protocol
             . $host
             . ($idx !== false
                ? implode('/', array_slice($parts, 0, $idx+1)) . '/'
                : '/');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Hóa đơn #<?php echo $invoiceId; ?></title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/assets/css/pages/invoice_upload.css">
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
            <form action="../../actions/invoice_requests/index.php?action=process_invoice_send"
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