<?php
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
    <!-- Title should be dynamic, passed from the including page -->
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Dashboard'; ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Updated Font Awesome -->

    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css"> 

    <script type="module" src="<?php echo $public_assets_path; ?>js/app.js" defer></script>
</head>
<body>
<div id="toast-container"></div> <!-- Container for toast notifications -->