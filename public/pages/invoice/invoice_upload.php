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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css"/>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/assets/css/components/upload.css"/>
</head>
<body>
<?php 
  include $private_layouts_path . 'admin_header.php';
  include $private_layouts_path . 'admin_sidebar.php';
?>
<main class="content-wrapper d-flex align-items-center justify-content-center" style="min-height:100vh; padding: 15px;">
  <div class="card shadow upload-card">
    <div class="card-header text-center">
      <h5 class="mb-0"><i class="fas fa-file-upload text-primary"></i> Upload Hóa đơn #<?php echo htmlspecialchars((string)$invoiceId, ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    <div class="card-body">
      <form action="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/handlers/invoice/index.php?action=process_invoice_send"
            class="dropzone" id="invoiceDropzone">
        <input type="hidden" name="invoice_id" value="<?php echo $invoiceId; ?>">
        <div class="dz-message text-center my-5">
          <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
          <p>Kéo thả file PDF hoặc nhấp để chọn<br><small class="text-muted">(Chỉ PDF, tối đa 5MB/file)</small></p>
        </div>
      </form>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <button id="startUpload" class="btn btn-primary btn-block mb-2 mb-md-0"><i class="fas fa-upload"></i> Upload</button>
        <a href="invoice_review.php?invoice_id=<?php echo $invoiceId; ?>" class="btn btn-outline-secondary btn-block">Hủy</a>
      </div>
      <div class="progress mt-3">
        <div id="uploadProgressBar" class="progress-bar bg-primary" role="progressbar" style="width:0%"></div>
      </div>
    </div>
  </div>
</main>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
window.invoiceId = <?php echo json_encode($invoiceId); ?>;
window.invoiceUploadUrl = '<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/handlers/invoice/index.php?action=process_invoice_send';
window.invoiceReviewUrl = '<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/pages/invoice/invoice_review.php?invoice_id=';
</script>
<script src="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>public/assets/js/pages/invoice/invoice_upload.js"></script>
</body>
</html>