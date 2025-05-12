<?php
// filepath: public/handlers/support/index.php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'fetch_support_requests',
    'get_support_request_details',
    'update_support_request'
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}
$privatePath = PRIVATE_ACTIONS_PATH . 'support/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}
require_once $privatePath;
exit;
