<?php
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
session_start();
header('Location: ' . BASE_URL . 'public/actions/auth/index.php?action=process_admin_logout');
exit;
?>
