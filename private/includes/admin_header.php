<?php
// Include our utility functions if they're not already included
if (!function_exists('get_base_path')) {
    require_once __DIR__ . '/../utils/functions.php';
}

// Use the standardized path functions for consistent path handling
$base_path = get_base_path();
$public_assets_path = $base_path . 'public/assets/';

// Page title handling
$page_title = isset($page_title) ? htmlspecialchars($page_title) : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Base styles, variables etc. -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/base.css">

    <!-- Layout styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/main_content.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/sidebar.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/layouts/header.css">

    <!-- Component styles -->
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/cards.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables/tables.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/tables/tables-buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/forms.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/modals.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/badges.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/components/toasts.css">
    <link rel="stylesheet" href="<?php echo $public_assets_path; ?>css/pages/dashboard.css">

    <script src="<?php echo $public_assets_path; ?>js/components/sidebar.js" defer></script>
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Hamburger & sidebar sẽ được include bên ngoài -->
</div>
</body>
</html>