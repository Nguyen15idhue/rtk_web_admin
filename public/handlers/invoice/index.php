<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'process_invoice_reject',
    'process_invoice_send'
];

if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/invoice/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}

require_once $privatePath;
exit;