<?php
// private/actions/station/actions.php
// Handle station action requests (update_station)

$bootstrap_data = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/';

Auth::ensureAuthorized('station_management_edit');

// Check if request is AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Phương thức yêu cầu không hợp lệ.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $base_url . 'public/pages/station/station_management.php');
    exit;
}

require_once __DIR__ . '/../../classes/StationManager/StationModel.php';
require_once __DIR__ . '/../../classes/StationManager/ManagerModel.php';

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
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => 'Thông tin trạm được cập nhật thành công.'
            ]);
            exit;
        } else {
            $_SESSION['message'] = 'Thông tin trạm được cập nhật thành công.';
            $_SESSION['message_type'] = 'success';
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Cập nhật thông tin trạm thất bại. Vui lòng kiểm tra logs hoặc thử lại.'
            ]);
            exit;
        } else {
            $_SESSION['message'] = 'Cập nhật thông tin trạm thất bại. Vui lòng kiểm tra logs hoặc thử lại.';
            $_SESSION['message_type'] = 'danger';
        }
    }
} elseif ($action === 'delete_undefined_stations') {
    // Action để xóa các trạm có trạng thái không xác định
    $result = $stationModel->deleteStationsWithUndefinedStatus();
    
    if ($result['success'] && $result['deleted_count'] > 0) {
        $_SESSION['message'] = "Đã xóa thành công {$result['deleted_count']} trạm có trạng thái không xác định.";
        $_SESSION['message_type'] = 'success';
        
        // Log the deletion for audit trail
        require_once __DIR__ . '/../../classes/Logger.php';
        Logger::info("Deleted stations with undefined status", [
            'action' => 'delete_undefined_stations',
            'deleted_count' => $result['deleted_count'],
            'deleted_ids' => $result['deleted_ids'],
            'admin_user' => $_SESSION['admin_user'] ?? 'unknown'
        ]);
    } elseif ($result['success'] && $result['deleted_count'] === 0) {
        $_SESSION['message'] = 'Không tìm thấy trạm nào có trạng thái không xác định để xóa.';
        $_SESSION['message_type'] = 'info';
    } else {
        $_SESSION['message'] = 'Xóa trạm thất bại. ' . ($result['error'] ?? 'Lỗi không xác định.');
        $_SESSION['message_type'] = 'danger';
        
        // Log the error
        require_once __DIR__ . '/../../classes/Logger.php';
        Logger::error("Failed to delete stations with undefined status", [
            'action' => 'delete_undefined_stations',
            'error' => $result['error'] ?? 'Unknown error',
            'admin_user' => $_SESSION['admin_user'] ?? 'unknown'
        ]);
    }
} else {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hành động không hợp lệ.'
        ]);
        exit;
    } else {
        $_SESSION['message'] = 'Hành động không hợp lệ.';
        $_SESSION['message_type'] = 'danger';
    }
}

// Only redirect for non-AJAX requests
if (!$isAjax) {
    header('Location: ' . $base_url . 'public/pages/station/station_management.php?tab=station');
}
exit;
