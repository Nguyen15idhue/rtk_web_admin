<?php
require_once __DIR__ . '/../../classes/GuideModel.php';
header('Content-Type: application/json');
$model = new GuideModel();
$ok = $model->update($_POST['id'], $_POST);
echo json_encode(['success' => (bool)$ok]);
