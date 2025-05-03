<?php
session_start();
header('Content-Type: application/json');

// Thêm require constants để có PRIVATE_ACTIONS_PATH
require_once __DIR__ . '/../../../private/config/constants.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'process_profile_fetch',        // <-- add this
    'process_profile_update',
    'process_password_change'
];
if (!in_array($action, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Dùng hằng số thay path cứng
$privatePath = PRIVATE_ACTIONS_PATH . '/setting/' . $action . '.php';

if (!file_exists($privatePath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Action not found']);
    exit;
}

require_once $privatePath;
exit;
