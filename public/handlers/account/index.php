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
    'search_users', 'manual_renew_account',
    'cron_update_stations' // <-- added cron action
];
if (!in_array($action, $allowed, true)) {
    api_error('Invalid action', 400);
}

// special handler for cron-driven station update
if ($action === 'cron_update_stations') {
    // Perform station update and redirect back to management page
    require_once dirname(__DIR__, 3) . '/private/api/rtk_system/account_api.php';
    fetchAndUpdateStations();
    // Set flash message
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['message'] = 'Danh sách trạm đã được làm mới.';
    $_SESSION['message_type'] = 'success';
    // Redirect back to station management page
    header('Location: ' . BASE_URL . 'public/pages/station/station_management.php');
    exit;
}

$privatePath = PRIVATE_ACTIONS_PATH . '/account/' . $action . '.php';
if (!file_exists($privatePath)) {
    api_error('Action not found', 404);
}

require_once $privatePath;
exit;