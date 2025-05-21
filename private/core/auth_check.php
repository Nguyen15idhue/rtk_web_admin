<?php
// filepath: private/core/auth_check.php
if (!isset($_SESSION['admin_id'])) {
    if (isset($base_url)) {
        header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    } else {
        header('Location: /public/pages/auth/admin_login.php'); 
    }
    exit;
}
?>
