<?php
// private/actions/station/management.php
// Handles fetching data and business logic for station management page
$base_url = $bootstrap_data['base_url'] ?? '/';

require_once __DIR__ . '/../../../private/classes/StationModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';
require_once __DIR__ . '/../../../private/classes/MountPointModel.php';
require_once __DIR__ . '/../../../private/classes/LocationModel.php';
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
// Fetch mountpoints from database for management
$mountPointModel = new MountPointModel();
$allMountPoints = $mountPointModel->getAllMountPoints();
// Fetch locations for assigning mountpoints
$locationModel = new LocationModel();
$allLocations = $locationModel->getAllLocations();

// Filters for mountpoint search
$mp_filters = [
    'q_mp' => isset($_GET['mp_q']) ? trim((string)$_GET['mp_q']) : ''
];
if ($mp_filters['q_mp'] !== '') {
    // Build location map for filtering by province name
    $locationMap = [];
    foreach ($allLocations as $loc) {
        $locationMap[$loc['id']] = $loc['province'];
    }
    // Apply filter across mountpoint properties and province
    $allMountPoints = array_filter($allMountPoints, function($mp) use ($mp_filters, $locationMap) {
        $q = mb_strtolower($mp_filters['q_mp']);
        return stripos(mb_strtolower($mp['mountpoint']), $q) !== false
            || stripos(mb_strtolower($mp['ip']), $q) !== false
            || stripos((string)$mp['port'], $q) !== false
            || (isset($locationMap[$mp['location_id']]) && stripos(mb_strtolower($locationMap[$mp['location_id']]), $q) !== false);
    });
}

// Session messages
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);

// Pagination for stations
$stations_page = isset($_GET['station_page']) ? max(1, (int)$_GET['station_page']) : 1;
$items_per_page = defined('DEFAULT_ITEMS_PER_PAGE') ? DEFAULT_ITEMS_PER_PAGE : 10;
$total_station_items = count($stations);
$total_pages_stations = (int) ceil($total_station_items / $items_per_page);
$stations = array_slice($stations, ($stations_page - 1) * $items_per_page, $items_per_page);

// Pagination for managers
$managers_page = isset($_GET['manager_page']) ? max(1, (int)$_GET['manager_page']) : 1;
$total_manager_items = count($allManagers);
$total_pages_managers = (int) ceil($total_manager_items / $items_per_page);
$allManagers = array_slice($allManagers, ($managers_page - 1) * $items_per_page, $items_per_page);

// Pagination for mount points
$mountpoints_page = isset($_GET['mountpoint_page']) ? max(1, (int)$_GET['mountpoint_page']) : 1;
$total_mountpoint_items = count($allMountPoints);
$total_pages_mountpoints = (int) ceil($total_mountpoint_items / $items_per_page);
$allMountPoints = array_slice($allMountPoints, ($mountpoints_page - 1) * $items_per_page, $items_per_page);

// Variables now available to view: $stations, $allManagers, $availableMountpoints, $filters, $message, $message_type, $page_title, $active_nav, $base_url
?>
