<?php
// private/actions/station/mountpoint_actions.php
$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap['base_url'] ?? '/';

require_once __DIR__ . '/../../classes/StationManager/MountPointModel.php';
require_once __DIR__ . '/../../classes/StationManager/StationModel.php';
Auth::ensureAuthorized('station_management_edit');

// Debug logging
error_log("mountpoint_actions.php - POST data: " . json_encode($_POST));
error_log("mountpoint_actions.php - GET data: " . json_encode($_GET));

$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log("mountpoint_actions.php - Action: " . $action);

$model = new MountPointModel();

// Check if request is AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

switch ($action) {
    case 'update_mountpoint':
        $mountpointId = $_POST['mountpoint_id'] ?? '';
        $locationId = isset($_POST['location_id']) && $_POST['location_id'] !== '' ? (int)$_POST['location_id'] : null;
        $ok = $model->updateMountPointLocation($mountpointId, $locationId);
        
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            if ($ok) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã cập nhật Mount Point thành công.'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Cập nhật Mount Point thất bại.'
                ]);
            }
            exit;
        } else {
            $_SESSION['message'] = $ok ? 'Đã cập nhật Mount Point.' : 'Cập nhật Mount Point thất bại.';
            $_SESSION['message_type'] = $ok ? 'success' : 'error';
            header("Location: {$base_url}public/pages/station/station_management.php?tab=mountpoint");
            exit;
        }
        
    case 'auto_update_locations':
        error_log("mountpoint_actions.php - Starting auto_update_locations");
        try {
            // Get mountpoints from API to process masterStationNames
            $stationModel = new StationModel();
            error_log("mountpoint_actions.php - Created StationModel");
            
            $mountpointsFromAPI = $stationModel->fetchAllMountpointsFromAPI();
            error_log("mountpoint_actions.php - Fetched " . count($mountpointsFromAPI) . " mountpoints from API");
            
            $result = $model->autoUpdateLocationsByMasterStationNames($mountpointsFromAPI);
            error_log("mountpoint_actions.php - Update result: " . json_encode($result));
            
            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Đã cập nhật tự động {$result['updated_count']}/{$result['total_processed']} Mount Point thành công.",
                        'data' => $result
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cập nhật tự động thất bại: ' . ($result['error'] ?? 'Unknown error'),
                        'data' => $result
                    ]);
                }
                exit;
            } else {
                if ($result['success']) {
                    $_SESSION['message'] = "Đã cập nhật tự động {$result['updated_count']}/{$result['total_processed']} Mount Point.";
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Cập nhật tự động thất bại: ' . ($result['error'] ?? 'Unknown error');
                    $_SESSION['message_type'] = 'error';
                }
                header("Location: {$base_url}public/pages/station/station_management.php?tab=mountpoint");
                exit;
            }
        } catch (Exception $e) {
            error_log("Error in auto_update_locations: " . $e->getMessage());
            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi hệ thống: ' . $e->getMessage()
                ]);
                exit;
            } else {
                $_SESSION['message'] = 'Lỗi hệ thống: ' . $e->getMessage();
                $_SESSION['message_type'] = 'error';
                header("Location: {$base_url}public/pages/station/station_management.php?tab=mountpoint");
                exit;
            }
        }
    default:
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Hành động không xác định.'
            ]);
            exit;
        } else {
            $_SESSION['message'] = 'Hành động không xác định.';
            $_SESSION['message_type'] = 'error';
            header("Location: {$base_url}public/pages/station/station_management.php?tab=mountpoint");
            exit;
        }
}
