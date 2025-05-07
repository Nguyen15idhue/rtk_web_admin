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

    <!-- Base styles, variables etc. -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css">

    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/main_content.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/sidebar.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/header.css"> 

    <!-- Component styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/cards.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/forms.css"> 
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/modals.css"> 
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/toast.css"> 
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css"> 
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/pages/dashboard.css"> <!-- Added dashboard.css -->

    <script src="<?php echo $public_assets_path; ?>js/components/sidebar.js" defer></script> <!-- Removed inline sidebar script; added external JS -->
    <script src="<?php echo $public_assets_path; ?>js/components/toasts.js" defer></script> <!-- Added toasts.js -->
    <script src="<?php echo $public_assets_path; ?>js/utils/errorHandler.js" defer></script> <!-- Added errorHandler.js -->
    <script src="<?php echo $public_assets_path; ?>js/utils/api.js" defer></script> <!-- Added api.js -->
    <script src="<?php echo $public_assets_path; ?>js/utils/helpers.js" defer></script> <!-- Add helpers.js so window.helpers is defined -->
</head>
<body>
<div id="toast-container"></div> <!-- Container for toast notifications -->
<div class="dashboard-wrapper">
    <!-- Hamburger & sidebar sẽ được include bên ngoài -->
</div>
</body>
</html>