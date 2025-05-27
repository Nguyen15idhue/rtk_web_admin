<?php
// filepath: public/handlers/voucher/index.php
header('Content-Type: application/json');
require_once dirname(__DIR__, 3) . '/private/config/constants.php';
require_once dirname(__DIR__, 3) . '/private/utils/functions.php';

$action = basename($_GET['action'] ?? '');
$allowed = [
    'fetch_vouchers',
    'get_voucher_details',
    'create_voucher',
    'update_voucher',
    'toggle_voucher_status',
    'delete_voucher',
    'get_locations', // Add new action
    'get_packages'   // Add new action
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}

$privatePath = PRIVATE_ACTIONS_PATH . '/voucher/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}

require_once $privatePath;
exit;
