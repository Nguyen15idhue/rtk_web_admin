<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../private/config/constants.php';
$action = basename($_GET['action'] ?? '');
$allowed = [
    'fetch_users',
    'get_user_details',
    'create_user',
    'update_user',
    'toggle_user_status'
];
if (!in_array($action, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$privatePath = PRIVATE_ACTIONS_PATH . '/user/' . $action . '.php';
if (!file_exists($privatePath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Action not found']);
    exit;
}

require_once $privatePath;
exit;
