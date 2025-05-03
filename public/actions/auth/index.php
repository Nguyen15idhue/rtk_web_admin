<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../private/config/constants.php';
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
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid auth action']);
    exit;
}

$privatePath = PRIVATE_ACTIONS_PATH . '/auth/' . $action . '.php';
if (!file_exists($privatePath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Auth action not found']);
    exit;
}

require_once $privatePath;
exit;
