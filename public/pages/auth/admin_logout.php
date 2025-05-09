<?php
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
header('Location: ' . BASE_URL . 'public/handlers/auth/index.php?action=process_admin_logout');
exit;
?>
