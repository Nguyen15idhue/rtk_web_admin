<?php
require_once __DIR__ . '/../../classes/GuideModel.php';
header('Content-Type: application/json');
// file upload?
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $up = __DIR__ . '/../../../public/uploads/guide/';
    if (!is_dir($up)) mkdir($up, 0755, true);
    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $fname = uniqid('guide-') . '.' . $ext;
    move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
    $_POST['thumbnail'] = $fname;
} else {
    // giữ ảnh cũ
    $_POST['thumbnail'] = $_POST['existing_thumbnail'] ?? '';
    unset($_POST['existing_thumbnail']);
}
$model = new GuideModel();
$ok = $model->update($_POST['id'], $_POST);
echo json_encode(['success' => (bool)$ok]);
