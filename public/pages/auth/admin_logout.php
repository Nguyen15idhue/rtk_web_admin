<?php
// Define the root path for includes
define('PRIVATE_PATH', dirname(__FILE__, 4) . '/private');

// Include the actual logout processing script
require_once(PRIVATE_PATH . '/actions/auth/process_admin_logout.php');
?>
