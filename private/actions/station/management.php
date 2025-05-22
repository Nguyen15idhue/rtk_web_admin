<?php
// private/actions/station/management.php
// Handles fetching data and business logic for station management page
$base_url = $bootstrap_data['base_url'] ?? '/';

require_once __DIR__ . '/../../../private/classes/StationModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';
Auth::ensureAuthorized('station_management_view');

// Page settings
$page_title = "Quản lý Trạm";
$active_nav = 'station_management';

// Filters
$filters = [
    'q' => isset($_GET['q']) ? trim((string)$_GET['q']) : '', // Use raw input
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

// Move stations with unknown status (-1) to bottom of the list
usort($stations, function($a, $b) {
    $sa = $a['status'] ?? null;
    $sb = $b['status'] ?? null;
    if ($sa === -1 && $sb !== -1) {
        return 1;
    } elseif ($sa !== -1 && $sb === -1) {
        return -1;
    }
    return 0;
});

$allManagers = $managerModel->getAllManagers();
$availableMountpoints = $stationModel->fetchMountpointsFromAPI();

// Session messages
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);

// Variables now available to view: $stations, $allManagers, $availableMountpoints, $filters, $message, $message_type, $page_title, $active_nav, $base_url
?>
