<?php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';
require_once __DIR__ . '/../../classes/ManagerModel.php';

$action = $_POST['action'] ?? '';
$model = new ManagerModel();

switch ($action) {
    case 'create_manager':
        $ok = $model->createManager(trim($_POST['name'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''));
        $_SESSION['message'] = $ok ? 'Manager created.' : 'Failed to create manager.';
        break;
    case 'update_manager':
        $ok = $model->updateManager($_POST['manager_id'] ?? '', trim($_POST['name'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''));
        $_SESSION['message'] = $ok ? 'Manager updated.' : 'Failed to update manager.';
        break;
    case 'delete_manager':
        $ok = $model->deleteManager($_POST['manager_id'] ?? '');
        $_SESSION['message'] = $ok ? 'Manager deleted.' : 'Failed to delete manager.';
        break;
    default:
        $_SESSION['message'] = 'Unknown action.';
}

$_SESSION['message_type'] = isset($ok) && $ok ? 'success' : 'error';
header("Location: {$base_url}public/pages/station/station_management.php");
exit;
