<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/GuideModel.php';
$model = new GuideModel();
$guide = $model->getOne($_GET['id'] ?? 0);
echo json_encode($guide);
