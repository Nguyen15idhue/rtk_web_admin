<?php
// Bootstrap and session
$bootstrap_data = require_once __DIR__ . '/../private/core/page_bootstrap.php';
$base_url       = $bootstrap_data['base_url'];

// Check if the admin is logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: " . $base_url . "public/pages/dashboard/dashboard.php");
    exit();
} else {
    header("Location: " . $base_url . "public/pages/auth/admin_login.php");
    exit();
}
?>
