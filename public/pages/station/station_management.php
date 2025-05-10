<?php
require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
require_once __DIR__ . '/../../../private/classes/StationModel.php';
require_once __DIR__ . '/../../../private/classes/ManagerModel.php';

// Get base_url from bootstrap data for redirect
$bootstrap_data = require __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'] ?? '/'; // Default to root if not set

// Updated authentication check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

$page_title = "Station Management";
$active_nav = 'station_management'; // For highlighting active link in sidebar

// --- Filters ---
$filters = [
    'q' => filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];

// Instantiate Models
$db = Database::getInstance()->getConnection(); // Get PDO connection
$stationModel = new StationModel(); // Assumes Database connection is handled via getInstance within model
$managerModel = new ManagerModel();

// Fetch data
$stations = $stationModel->getAllStations();
if ($filters['q'] !== '') {
    $stations = array_filter($stations, function($st) use ($filters) {
        return stripos($st['station_name'], $filters['q']) !== false
            || stripos($st['identificationName'] ?? '', $filters['q']) !== false
            || stripos($st['manager_name'] ?? '', $filters['q']) !== false;
    });
}

$allManagers = $managerModel->getAllManagers(); // For manager name datalist
$availableMountpoints = $stationModel->fetchMountpointsFromAPI(); // Fetch all mountpoints

// Handle session messages (e.g., success/error after update)
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;
unset($_SESSION['message']);
unset($_SESSION['message_type']);

require_once PRIVATE_LAYOUTS_PATH . '/admin_header.php';
require_once PRIVATE_LAYOUTS_PATH . '/admin_sidebar.php';
?>

<main class="content-wrapper">
    <div class="content-header">
        <h2><?php echo htmlspecialchars($page_title); ?></h2>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="" class="filter-bar" style="margin-bottom:15px;">
        <input type="search" name="q" placeholder="Tìm kiếm trạm..." value="<?php echo htmlspecialchars($filters['q']); ?>">
        <button type="submit" class="btn btn-primary">Tìm</button>
        <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-secondary">Xóa lọc</a>
    </form>

    <!-- Bulk Export Form -->
    <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
        <input type="hidden" name="table_name" value="stations">
        <div class="bulk-actions-bar" style="margin-bottom:15px;">
            <button type="submit" name="export_selected" class="btn btn-info">Xuất mục đã chọn</button>
            <button type="submit" name="export_all" class="btn btn-success">Xuất tất cả</button>
        </div>

        <div class="transactions-table-wrapper">
            <table id="stationsTable" class="transactions-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Station Name</th>
                        <th>Identification Name</th>
                        <th>Current Manager</th>
                        <th>Current Mountpoint</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stations)): ?>
                        <tr><td colspan="7">No stations found.</td></tr>
                    <?php else: foreach ($stations as $station): ?>
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo htmlspecialchars($station['id']); ?>"></td>
                            <form action="<?php echo BASE_URL; ?>public/handlers/station/station_actions.php" method="POST">
                                <input type="hidden" name="action" value="update_station">
                                <input type="hidden" name="station_id" value="<?php echo htmlspecialchars($station['id']); ?>">
                                <td><?php echo htmlspecialchars($station['id']); ?></td>
                                <td><?php echo htmlspecialchars($station['station_name']); ?></td>
                                <td><?php echo htmlspecialchars($station['identificationName'] ?? 'N/A'); ?></td>
                                <td>
                                    <input list="manager_names_<?php echo $station['id']; ?>" name="manager_name"
                                           class="form-control" value="<?php echo htmlspecialchars($station['manager_name'] ?? ''); ?>"
                                           placeholder="Enter manager name">
                                    <datalist id="manager_names_<?php echo $station['id']; ?>">
                                        <?php foreach ($allManagers as $manager): ?>
                                            <option value="<?php echo htmlspecialchars($manager['name']); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </td>
                                <td>
                                    <select name="mountpoint_details" class="form-control">
                                        <option value="">-- Select Mountpoint --</option>
                                        <?php foreach ($availableMountpoints as $mp): 
                                            $mountpointValueJson = json_encode([
                                                'id'=>$mp['id'],'name'=>$mp['name'],
                                                'masterStationNames'=>$mp['masterStationNames']??[]
                                            ]);
                                            $isSelected = isset($station['mountpoint_id'])
                                                && (string)$station['mountpoint_id']===(string)$mp['id'];
                                            $displayText = htmlspecialchars($mp['name'])
                                                .' (Masters: '.htmlspecialchars(implode(', ',$mp['masterStationNames']??[])).')';
                                        ?>
                                            <option value='<?php echo htmlspecialchars($mountpointValueJson,ENT_QUOTES,'UTF-8'); ?>'
                                                <?php echo $isSelected?'selected':''; ?>>
                                                <?php echo $displayText; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</main>

<?php
require_once PRIVATE_LAYOUTS_PATH . '/admin_footer.php';
?>

<!-- JS to toggle all checkboxes -->
<script>
document.getElementById('selectAll').addEventListener('change', function(){
    document.querySelectorAll('.rowCheckbox').forEach(cb=>cb.checked = this.checked);
});
</script>
