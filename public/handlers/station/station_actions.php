<?php
require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
require_once __DIR__ . '/../../../private/classes/StationModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';

// Get base_url from bootstrap data for redirect and other uses
$bootstrap_data = require __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/';

// Updated authentication check - ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not AJAX, redirect to login
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    }
    exit;
}

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $base_url . 'public/pages/station/station_management.php');
    exit;
}

// Instantiate Models
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

    $mountpoint_id_from_api = null; // Renamed for clarity, this is the ID from API to be stored in station.mountpoint_id

    if (!empty($mountpoint_details_json)) {
        $mountpoint_data = json_decode($mountpoint_details_json, true);
        // We only care about the 'id' from the mountpoint details now
        if (json_last_error() === JSON_ERROR_NONE && isset($mountpoint_data['id'])) {
            $mountpoint_id_from_api = $mountpoint_data['id'];
        } else {
            // If mountpoint_details_json is not empty but invalid or missing id, treat as an error or clear selection
            if ($mountpoint_details_json !== '') { // only error if it was not an intentional empty selection
                $_SESSION['message'] = 'Invalid mountpoint data received.';
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $base_url . 'public/pages/station/station_management.php');
                exit;
            }
        }
    }

    // Proceed to update the station with only manager_id and mountpoint_id (from API)
    $success = $stationModel->updateStation(
        $station_id,
        $manager_id, // This can be null if input was empty
        $mountpoint_id_from_api // This can be null if no mountpoint selected or if details were empty/invalid
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

// Redirect back to the station management page
header('Location: ' . $base_url . 'public/pages/station/station_management.php');
exit;
?>
