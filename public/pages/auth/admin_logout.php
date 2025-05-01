<?php
session_start();

// Include the utility functions for standardized paths
require_once __DIR__ . '/../../../private/utils/functions.php';

// Get standardized base path
$base_path = get_base_path();

// Redirect to the logout action using standardized path
header('Location: ' . $base_path . 'public/actions/auth/index.php?action=process_admin_logout');
exit;
?>
