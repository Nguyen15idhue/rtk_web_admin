<?php
// Ensure $base_path is defined as a string (fallback to '/rtk_web_admin/')
if (!isset($base_path) || !is_string($base_path)) {
    // Attempt to determine base path dynamically if possible, otherwise default
    // This might need adjustment based on your server setup / framework
    $script_name = $_SERVER['SCRIPT_NAME']; // e.g., /rtk_web_admin/public/pages/dashboard/dashboard.php
    $base_path_parts = explode('/', $script_name);
    // Assuming structure is /project_root/public/pages/file.php
    // Adjust the slice index based on your actual structure
    if (count($base_path_parts) >= 3) {
        $base_path = '/' . $base_path_parts[1] . '/'; // Assumes project root is the first segment
    } else {
        $base_path = '/'; // Fallback default
    }
}

// Ensure $base_path ends with a slash
if (substr($base_path, -1) !== '/') {
    $base_path .= '/';
}
$public_assets_path = $base_path . 'public/assets/';

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