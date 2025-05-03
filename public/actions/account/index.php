<?php
// Front-controller for account actions
session_start();
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
$action = basename($_GET['action'] ?? '');
$allowed = [
    'create_account', 'delete_account', 'fetch_accounts',
    'get_account_details', 'handle_account_list',
    'toggle_account_status', 'update_account',
    'search_users'
];
if (!in_array($action, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$privatePath = PRIVATE_ACTIONS_PATH . '/account/' . $action . '.php';
if (!file_exists($privatePath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Action not found']);
    exit;
}

require_once $privatePath;
exit;