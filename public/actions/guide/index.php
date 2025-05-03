<?php
header('Content-Type: application/json');
// add constants for PRIVATE_ACTIONS_PATH
require_once __DIR__ . '/../../../private/config/constants.php';
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'fetch':
        require_once PRIVATE_ACTIONS_PATH . '/guide/fetch_guides.php';
        break;
    case 'toggle':
        require_once PRIVATE_ACTIONS_PATH . '/guide/toggle_guide_status.php';
        break;
    case 'get_details':
        require_once PRIVATE_ACTIONS_PATH . '/guide/get_guide_details.php';
        break;
    case 'create':
        require_once PRIVATE_ACTIONS_PATH . '/guide/create_guide.php';
        break;
    case 'update':
        require_once PRIVATE_ACTIONS_PATH . '/guide/update_guide.php';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
exit;
