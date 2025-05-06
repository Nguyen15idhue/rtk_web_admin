<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'fetch_users',
    'get_user_details',
    'create_user',
    'update_user',
    'toggle_user_status'
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/user/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}

require_once $privatePath;
exit;
