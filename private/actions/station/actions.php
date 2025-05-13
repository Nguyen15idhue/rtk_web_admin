<?php
// private/actions/station/actions.php
// Handle station action requests (update_station)

$bootstrap_data = require_once __DIR__ . '/../../core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/';

require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthenticated();

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
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
        $_SESSION['message'] = 'Station ID is missing.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . $base_url . 'public/pages/station/station_management.php');
        exit;
    }

    $manager_id = null;
    if (!empty($manager_name_input)) {
        $manager_id = $managerModel->findManagerIdByName($manager_name_input);
        if (!$manager_id) {
            $_SESSION['message'] = "Manager with name '{$manager_name_input}' not found. Please ensure the manager exists.";
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
                $_SESSION['message'] = 'Invalid mountpoint data received.';
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
        $_SESSION['message'] = 'Station information updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to update station information. Please check logs or try again.';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'Invalid action specified.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: ' . $base_url . 'public/pages/station/station_management.php');
exit;
