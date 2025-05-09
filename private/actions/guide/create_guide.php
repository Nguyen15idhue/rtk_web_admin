<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class

header('Content-Type: application/json');

// Use Auth class for authentication
Auth::ensureAuthenticated();

// add slugify helper
function slugify($text) {
    // transliterate to ASCII
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    // replace non letters/digits with hyphens
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // remove unwanted chars
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim hyphens, lowercase, collapse duplicates
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

try {
    // handle file upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $up = UPLOADS_PATH . 'guide/';
        if (!is_dir($up)) mkdir($up, 0755, true);
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $fname = uniqid('guide-') . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
        $_POST['thumbnail'] = $fname;
    }

    // sanitize slug
    if (isset($_POST['slug'])) {
        $_POST['slug'] = slugify($_POST['slug']);
    }

    // prepare data
    $data = $_POST;
    $data['author_id'] = $_SESSION['admin_id'];

    $model = new GuideModel();
    $ok = $model->create($data);
    if ($ok) {
        api_success([], 'Guide created successfully');
    } else {
        api_error('Error creating guide', 500);
    }
} catch (\Throwable $e) {
    error_log(sprintf(
        "Critical [create_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error creating guide: ' . $e->getMessage(), 500);
}
