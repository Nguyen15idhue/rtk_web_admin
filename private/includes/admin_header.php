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
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/header.css"> <!-- Added header.css link -->

    <!-- Component styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/cards.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables/tables-buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/forms.css"> <!-- Added forms.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/modals.css"> <!-- Added modals.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/badges.css"> <!-- Added badges.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/toasts.css"> <!-- Added toasts.css -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css"> <!-- Removed inline styles; added external CSS -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/pages/dashboard.css"> <!-- Added dashboard.css -->

    <script src="<?php echo $public_assets_path; ?>js/components/sidebar.js" defer></script> <!-- Removed inline sidebar script; added external JS -->
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Hamburger & sidebar sẽ được include bên ngoài -->
</div>
</body>
</html>