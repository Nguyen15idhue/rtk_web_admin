<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';
$action = basename($_GET['action'] ?? '');
$allowed = [
    'process_admin_login',
    'process_admin_logout',
    'fetch_permissions',
    'process_permissions_update',
    'process_admin_create',
    'process_admin_update',
    'process_admin_delete'
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid auth action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/auth/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Auth action not found', 404);
}

switch ($action) {
    case 'process_admin_login':
        // validate credentials, fetch $adminId, $adminRole, etc.
        // prevent session fixation by regenerating ID
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $adminId;
        $_SESSION['admin_username'] = $adminUsername;
        $_SESSION['admin_role']     = $adminRole;
        // redirect to dashboard or set error
        break;
    // other cases
}

require_once $privatePath;
exit;