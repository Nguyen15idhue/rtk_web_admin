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
    'process_admin_delete',
    'process_role_create' // Added new action
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid auth action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/auth/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Auth action not found', 404);
}

require_once $privatePath;
exit;