<?php
require_once __DIR__ . '/../../classes/GuideModel.php';
header('Content-Type: application/json');
$model = new GuideModel();
$ok = $model->toggleStatus($_POST['id'], $_POST['status']);
echo json_encode(['success' => (bool)$ok]);
