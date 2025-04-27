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
$model = new GuideModel();
$data = $_POST;
$data['author_id'] = $_SESSION['admin_id'];
$ok = $model->create($data);
echo json_encode(['success' => (bool)$ok]);
