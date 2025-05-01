<?php
session_start();
require_once __DIR__ . '/../../classes/GuideModel.php';
// Require logged‑in admin
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
// handle file upload
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $up = __DIR__ . '/../../../public/uploads/guide/';
    if (!is_dir($up)) mkdir($up, 0755, true);
    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $fname = uniqid('guide-') . '.' . $ext;
    move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
    $_POST['thumbnail'] = $fname;
}
// prepare data
$model = new GuideModel();
$data = $_POST;
$data['author_id'] = $_SESSION['admin_id'];
$ok = $model->create($data);
echo json_encode(['success' => (bool)$ok]);
