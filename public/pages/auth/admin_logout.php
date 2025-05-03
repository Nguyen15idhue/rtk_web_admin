<?php
require_once __DIR__ . '/../../../private/config/constants.php';
session_start();
header('Location: ' . BASE_URL . 'public/actions/auth/index.php?action=process_admin_logout');
exit;
?>
