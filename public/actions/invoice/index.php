<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
$action = basename($_GET['action'] ?? '');
$allowed = [
    'process_transaction_approve',
    'process_transaction_reject',
    'process_transaction_revert'
];
if (!in_array($action, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$privatePath = PRIVATE_ACTIONS_PATH . '/purchase/' . $action . '.php';
if (!file_exists($privatePath)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Action not found']);
    exit;
}

require_once $privatePath;
exit;
