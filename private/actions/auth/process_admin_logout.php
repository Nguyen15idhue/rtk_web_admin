<?php
// Include centralized bootstrap (starts session, validates admin, idle‐timeout, etc.)
$bootstrap = require_once __DIR__ . '/../../includes/page_bootstrap.php';

// Deactivate the session if admin_id is set
if (isset($_SESSION['admin_id'])) {
    deactivateSession(session_id());
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirect to the login page
header("Location: " . $bootstrap['base_url'] . "public/pages/auth/admin_login.php");
exit();
?>
