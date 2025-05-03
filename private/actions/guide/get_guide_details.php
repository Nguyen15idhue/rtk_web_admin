<?php
$paths = require __DIR__ . '/../../includes/page_bootstrap.php';

function get_guide_details(int $id): array {
    $model = new GuideModel();
    return $model->getOne($id) ?: [];
}

if (isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(get_guide_details((int)$_GET['id']));
    exit;
}
