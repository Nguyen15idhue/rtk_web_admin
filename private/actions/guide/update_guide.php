<?php
require_once __DIR__ . '/../../includes/page_bootstrap.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthorized(['admin']); // Only admins can update guides

header('Content-Type: application/json');

// add slugify helper
function slugify($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

try {
    if (empty($_POST['id'])) {
        abort('Guide ID is required', 400);
    }

    // file upload?
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $up = UPLOADS_PATH . 'guide/';
        if (!is_dir($up)) mkdir($up, 0755, true);
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $fname = uniqid('guide-') . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $up . $fname);
        $_POST['thumbnail'] = $fname;
    } else {
        // giữ ảnh cũ
        $_POST['thumbnail'] = $_POST['existing_thumbnail'] ?? '';
        unset($_POST['existing_thumbnail']);
    }

    // sanitize slug
    if (isset($_POST['slug'])) {
        $_POST['slug'] = slugify($_POST['slug']);
    }

    $model = new GuideModel();
    $ok = $model->update($_POST['id'], $_POST);
    if ($ok) {
        api_success([], 'Guide updated successfully');
    } else {
        api_error('Error updating guide', 500);
    }
} catch (\Throwable $e) {
    // Bổ sung logging chi tiết
    error_log(sprintf(
        "Critical [update_guide.php:%d]: %s\nStack trace:\n%s",
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    api_error('Error updating guide: ' . $e->getMessage(), 500);
}
