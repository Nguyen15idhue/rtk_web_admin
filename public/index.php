<?php
session_start();

// Include the standardized path function
require_once __DIR__ . '/../private/utils/functions.php';

// Get base path using our standardized function
$base_path = get_base_path();

// Check if the admin is logged in
if (isset($_SESSION['admin_id'])) {
    // If logged in, redirect to admin dashboard with standardized path
    header('Location: ' . $base_path . 'public/pages/dashboard/dashboard.php');
    exit();
} else {
    // If not logged in, redirect to admin login page with standardized path
    header('Location: ' . $base_path . 'public/pages/auth/admin_login.php');
    exit();
}
?>
