<?php
$paths = require __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');
$model = new GuideModel();
$list = $model->getAll($_GET['search'] ?? '');
echo json_encode($list);
