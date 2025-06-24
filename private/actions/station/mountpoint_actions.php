<?php
// private/actions/station/mountpoint_actions.php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';

require_once __DIR__ . '/../../classes/MountPointModel.php';
Auth::ensureAuthorized('station_management_edit');
$action = $_POST['action'] ?? '';
$model = new MountPointModel();

switch ($action) {
    case 'update_mountpoint':
        $mountpointId = $_POST['mountpoint_id'] ?? '';
        $locationId = isset($_POST['location_id']) && $_POST['location_id'] !== '' ? (int)$_POST['location_id'] : null;
        $ok = $model->updateMountPointLocation($mountpointId, $locationId);
        $_SESSION['message'] = $ok ? 'Đã cập nhật Mount Point.' : 'Cập nhật Mount Point thất bại.';
        break;
    default:
        $_SESSION['message'] = 'Hành động không xác định.';
}
$_SESSION['message_type'] = isset($ok) && $ok ? 'success' : 'error';
header("Location: {$base_url}public/pages/station/station_management.php");
exit;
