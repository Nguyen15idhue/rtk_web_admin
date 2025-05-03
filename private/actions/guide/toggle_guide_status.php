<?php
$paths = require __DIR__ . '/../../includes/page_bootstrap.php';
header('Content-Type: application/json');
$model = new GuideModel();
$ok = $model->toggleStatus($_POST['id'], $_POST['status']);
echo json_encode(['success' => (bool)$ok]);
