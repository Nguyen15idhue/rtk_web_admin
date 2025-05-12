<?php
// private/actions/station/management.php
// Handles fetching data and business logic for station management page

$bootstrap_data = require __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/';

require_once __DIR__ . '/../../../private/classes/StationModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
Auth::ensureAuthenticated();

// Page settings
$page_title = "Quản lý Trạm";
$active_nav = 'station_management';

// Filters
$filters = [
    'q' => filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];

// Instantiate models and fetch data
$stationModel = new StationModel();
$managerModel = new ManagerModel();

$stations = $stationModel->getAllStations();
if ($filters['q'] !== '') {
    $stations = array_filter($stations, function($st) use ($filters) {
        return stripos($st['station_name'], $filters['q']) !== false
            || stripos($st['identificationName'] ?? '', $filters['q']) !== false
            || stripos($st['manager_name'] ?? '', $filters['q']) !== false;
    });
}

$allManagers = $managerModel->getAllManagers();
$availableMountpoints = $stationModel->fetchMountpointsFromAPI();

// Session messages
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);

// Variables now available to view: $stations, $allManagers, $availableMountpoints, $filters, $message, $message_type, $page_title, $active_nav, $base_url
?>
