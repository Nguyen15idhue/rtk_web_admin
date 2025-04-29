<?php
session_start();
header('Location: ../../actions/auth/index.php?action=process_admin_logout');
exit;
?>
