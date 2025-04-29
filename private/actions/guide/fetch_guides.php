<?php
require_once __DIR__ . '/../../classes/GuideModel.php';
header('Content-Type: application/json');
$model = new GuideModel();
$list = $model->getAll($_GET['search'] ?? '');
echo json_encode($list);
