<?php
// private/actions/station/actions.php
// Handle station action requests (update_station)

$bootstrap_data = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/';

Auth::ensureAuthorized('station_management_edit');

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Phương thức yêu cầu không hợp lệ.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $base_url . 'public/pages/station/station_management.php');
    exit;
}

require_once __DIR__ . '/../../classes/StationModel.php';
require_once __DIR__ . '/../../classes/ManagerModel.php';

$stationModel = new StationModel();
$managerModel = new ManagerModel();
$action = $_POST['action'] ?? null;

if ($action === 'update_station') {
    $station_id = $_POST['station_id'] ?? null;
    $manager_name_input = trim($_POST['manager_name'] ?? '');
    $mountpoint_details_json = $_POST['mountpoint_details'] ?? null;

    if (empty($station_id)) {
        $_SESSION['message'] = 'Thiếu ID trạm.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . $base_url . 'public/pages/station/station_management.php');
        exit;
    }

    $manager_id = null;
    if (!empty($manager_name_input)) {
        $manager_id = $managerModel->findManagerIdByName($manager_name_input);
        if (!$manager_id) {
            $_SESSION['message'] = "Không tìm thấy người quản lý với tên '{$manager_name_input}'. Vui lòng đảm bảo người quản lý tồn tại.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $base_url . 'public/pages/station/station_management.php');
            exit;
        }
    }

    $mountpoint_id_from_api = null;
    if (!empty($mountpoint_details_json)) {
        $mountpoint_data = json_decode($mountpoint_details_json, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($mountpoint_data['id'])) {
            $mountpoint_id_from_api = $mountpoint_data['id'];
        } else {
            if ($mountpoint_details_json !== '') {
                $_SESSION['message'] = 'Dữ liệu mountpoint nhận được không hợp lệ.';
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $base_url . 'public/pages/station/station_management.php');
                exit;
            }
        }
    }

    $success = $stationModel->updateStation(
        $station_id,
        $manager_id,
        $mountpoint_id_from_api
    );

    if ($success) {
        $_SESSION['message'] = 'Thông tin trạm được cập nhật thành công.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Cập nhật thông tin trạm thất bại. Vui lòng kiểm tra logs hoặc thử lại.';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Hành động không hợp lệ.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: ' . $base_url . 'public/pages/station/station_management.php');
exit;
