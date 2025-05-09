<?php
// Front-controller for account actions
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'create_account', 'delete_account', 'fetch_accounts',
    'get_account_details', 'handle_account_list',
    'toggle_account_status', 'update_account',
    'search_users', 'manual_renew_account' // Add new action
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/account/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}

require_once $privatePath;
exit;