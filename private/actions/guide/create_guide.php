<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';

header('Content-Type: application/json');

try {
    if (empty($_SESSION['admin_id'])) {
        abort('Unauthorized', 401);
    }

    // handle file upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $up = UPLOADS_PATH . 'guide/';
        if (!is_dir($up)) mkdir($up, 0755, true);
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $fname = uniqid('guide-') . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
        $_POST['thumbnail'] = $fname;
    }

    // prepare data
    $data = $_POST;
    $data['author_id'] = $_SESSION['admin_id'];

    $model = new GuideModel();
    $ok = $model->create($data);
    echo json_encode(['success' => (bool)$ok]);
} catch (\Throwable $e) {
    // Bổ sung logging chi tiết
    error_log(sprintf(
        "Critical [create_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    abort('Error creating guide: '.$e->getMessage(), 500);
}
