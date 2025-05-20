<?php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';
require_once __DIR__ . '/../../classes/ManagerModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';
Auth::ensureAuthorized('station_management_edit');
$action = $_POST['action'] ?? '';
$model = new ManagerModel();

switch ($action) {
    case 'create_manager':
        $ok = $model->createManager(trim($_POST['name'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''));
        $_SESSION['message'] = $ok ? 'Đã tạo người quản lý.' : 'Tạo người quản lý thất bại.';
        break;
    case 'update_manager':
        $ok = $model->updateManager($_POST['manager_id'] ?? '', trim($_POST['name'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''));
        $_SESSION['message'] = $ok ? 'Đã cập nhật người quản lý.' : 'Cập nhật người quản lý thất bại.';
        break;
    case 'delete_manager':
        $ok = $model->deleteManager($_POST['manager_id'] ?? '');
        $_SESSION['message'] = $ok ? 'Đã xóa người quản lý.' : 'Xóa người quản lý thất bại.';
        break;
    default:
        $_SESSION['message'] = 'Hành động không xác định.';
}

$_SESSION['message_type'] = isset($ok) && $ok ? 'success' : 'error';
header("Location: {$base_url}public/pages/station/station_management.php");
exit;
