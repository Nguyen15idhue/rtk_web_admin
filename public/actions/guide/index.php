<?php
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'fetch':
        require_once __DIR__ . '/../../../private/actions/guide/fetch_guides.php';
        break;
    case 'toggle':
        require_once __DIR__ . '/../../../private/actions/guide/toggle_guide_status.php';
        break;
    case 'get_details':
        require_once __DIR__ . '/../../../private/actions/guide/get_guide_details.php';
        break;
    case 'create':
        require_once __DIR__ . '/../../../private/actions/guide/create_guide.php';
        break;
    case 'update':
        require_once __DIR__ . '/../../../private/actions/guide/update_guide.php';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
exit;
