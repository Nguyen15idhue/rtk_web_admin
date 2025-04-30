<?php
session_start();

// Check if the admin is logged in
if (isset($_SESSION['admin_id'])) {
    // If logged in, redirect to admin dashboard (use absolute path)
    header("Location: pages/dashboard/dashboard.php"); // Example admin dashboard path
    exit();
} else {
    // If not logged in, redirect to admin login page (use relative path)
    header("Location: pages/auth/admin_login.php"); // Use relative path
    exit();
}
?>
