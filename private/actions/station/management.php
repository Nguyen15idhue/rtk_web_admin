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
    'status' => isset($_GET['status']) && $_GET['status'] !== '' ? (string)$_GET['status'] : null, // Add status filter
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

// Filter by status if set
if ($filters['status'] !== null) {
    $stations = array_filter($stations, function($st) use ($filters) {
        return isset($st['status']) && (string)$st['status'] === $filters['status'];
    });
}

// Sort stations by status: Online (1), No Data (2), Offline (3), Stop (0), Unknown (-1 or null)
usort($stations, function($a, $b) {
    $status_a = $a['status'] ?? null;
    $status_b = $b['status'] ?? null;

    // Define sort order priority (lower number = higher in list)
    $priority = function($status) {
        if ($status === null || $status === -1) return 5; // Unknown or null status
        if ($status === 0) return 4; // Stop
        if ($status === 3) return 3; // Offline
        if ($status === 2) return 2; // No Data
        if ($status === 1) return 1; // Online
        return 0; // Default for any other statuses (comes first)
    };

    $priority_a = $priority($status_a);
    $priority_b = $priority($status_b);

    if ($priority_a === $priority_b) {
        // Optional: secondary sort by ID or name if priorities are the same
        // return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
        return 0;
    }
    return ($priority_a < $priority_b) ? -1 : 1;
});

$allManagers = $managerModel->getAllManagers();
$availableMountpoints = $stationModel->fetchAllMountpointsFromAPI(); // Sử dụng phương thức mới để lấy tất cả mountpoints (KHÔNG paginate cho dropdown)
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

// Pagination for mount points - sử dụng API data cho hiển thị
$mountpoints_page = isset($_GET['mountpoint_page']) ? max(1, (int)$_GET['mountpoint_page']) : 1;

// Để hiển thị đầy đủ, sử dụng API mountpoints cho table management
$mountpointsForTable = $availableMountpoints; // Sử dụng dữ liệu từ API
$total_mountpoint_items = count($mountpointsForTable);
$total_pages_mountpoints = (int) ceil($total_mountpoint_items / $items_per_page);

// Paginate API data for table display
$mountpointsForTable = array_slice($mountpointsForTable, ($mountpoints_page - 1) * $items_per_page, $items_per_page);

// Keep database mountpoints for location mapping (nếu cần)
$allMountPoints = $mountPointModel->getAllMountPoints();

// Variables now available to view: $stations, $allManagers, $availableMountpoints, $mountpointsForTable, $allMountPoints, $filters, $message, $message_type, $page_title, $active_nav, $base_url
?>
