<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\layouts\admin_header.php

// Perform authentication check at the very beginning of the header
require_once __DIR__ . '/../core/auth_check.php';

// Include base constants
require_once __DIR__ . '/../config/constants.php';

// Override dynamic base path and assets path
$base_path = BASE_URL;
$public_assets_path = BASE_URL . 'public/assets/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Admin Dashboard for RTK System'; ?>">
    <!-- Title should be dynamic, passed from the including page -->
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Dashboard'; ?></title>    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Updated Font Awesome -->

    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $public_assets_path; ?>favicon.ico">

    <?php
    // Include additional CSS files if defined
    if (isset($additional_css) && is_array($additional_css)) {
        foreach ($additional_css as $css_file) {
            echo '<link rel="stylesheet" href="' . $public_assets_path . 'css/' . htmlspecialchars($css_file) . '">' . "\n    ";
        }
    }
    ?>    <script type="module" src="<?php echo $public_assets_path; ?>js/app.js" crossorigin="anonymous" defer></script>
    <script src="<?php echo $public_assets_path; ?>js/components/content_header.js" defer></script>
</head>
<body>
<div id="toast-container"></div> <!-- Container for toast notifications -->