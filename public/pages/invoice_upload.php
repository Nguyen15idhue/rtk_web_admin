<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth/admin_login.php');
    exit;
}

require_once __DIR__ . '/../../private/config/database.php';
require_once __DIR__ . '/../../private/classes/Database.php';

$invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
if ($invoiceId <= 0) {
    header('Location: invoice_review.php');
    exit;
}

$private_includes_path = __DIR__ . '/../../private/includes/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload Hóa đơn #<?php echo $invoiceId; ?></title>
    <link rel="stylesheet" href="base.css"><!-- adjust as needed -->
</head>
<body>
<?php 
include $private_includes_path . 'admin_header.php';
include $private_includes_path . 'admin_sidebar.php';
?>
<main class="content-wrapper">
    <h2>Upload Hóa đơn #<?php echo $invoiceId; ?></h2>
    <form action="../../private/actions/invoice/process_invoice_send.php"
          method="post" enctype="multipart/form-data">
        <input type="hidden" name="invoice_id" value="<?php echo $invoiceId; ?>">
        <div>
            <label for="invoice_file">Chọn file PDF:</label>
            <input type="file" name="invoice_file" id="invoice_file"
                   accept="application/pdf" required>
        </div>
        <button type="submit">Upload và Duyệt</button>
        <a href="invoice_review.php">Hủy</a>
    </form>
</main>
</body>
</html>
