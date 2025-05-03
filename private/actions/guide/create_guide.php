<?php
$paths = require __DIR__ . '/../../includes/page_bootstrap.php';
$model = new GuideModel();
// Require logged‑in admin
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
// handle file upload
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $up = $paths['base_path'] . '/../public/uploads/guide/';
    if (!is_dir($up)) mkdir($up, 0755, true);
    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $fname = uniqid('guide-') . '.' . $ext;
    move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
    $_POST['thumbnail'] = $fname;
}
// prepare data
$data = $_POST;
$data['author_id'] = $_SESSION['admin_id'];
$ok = $model->create($data);
echo json_encode(['success' => (bool)$ok]);
