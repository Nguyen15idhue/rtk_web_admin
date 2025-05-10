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

// Instantiate Models
$db = Database::getInstance()->getConnection(); // Get PDO connection
$stationModel = new StationModel(); // Assumes Database connection is handled via getInstance within model
$managerModel = new ManagerModel();

// Fetch data
$stations = $stationModel->getAllStations();
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

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Stations List</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($stations)): ?>
                        <p>No stations found.</p>
                    <?php else: ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Station Name</th>
                                    <th>Identification Name</th> <!-- Changed header -->
                                    <th>Current Manager</th>
                                    <th>Current Mountpoint</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stations as $station): ?>
                                    <form action="<?php echo BASE_URL; ?>public/handlers/station/station_actions.php" method="POST">
                                    <input type="hidden" name="action" value="update_station">
                                    <input type="hidden" name="station_id" value="<?php echo htmlspecialchars($station['id']); ?>">
                                    <tr>
                                        <td><?php echo htmlspecialchars($station['id']); ?></td>
                                        <td><?php echo htmlspecialchars($station['station_name']); ?></td>
                                        <td><?php echo htmlspecialchars($station['identificationName'] ?? 'N/A'); ?></td>
                                        
                                        <td>
                                            <input list="manager_names_<?php echo $station['id']; ?>" name="manager_name" class="form-control" value="<?php echo htmlspecialchars($station['manager_name'] ?? ''); ?>" placeholder="Enter manager name">
                                            <datalist id="manager_names_<?php echo $station['id']; ?>">
                                                <?php foreach ($allManagers as $manager): ?>
                                                    <option value="<?php echo htmlspecialchars($manager['name']); ?>">
                                                <?php endforeach; ?>
                                            </datalist>
                                        </td>
                                        <td>
                                            <select name="mountpoint_details" class="form-control">
                                                <option value="">-- Select Mountpoint --</option>
                                                <?php foreach ($availableMountpoints as $mp): ?>
                                                    <?php 
                                                        $mountpointValueJson = json_encode([
                                                            'id' => $mp['id'], 
                                                            'name' => $mp['name'], 
                                                            'masterStationNames' => $mp['masterStationNames'] ?? []
                                                        ]);
                                                        // Updated isSelected logic to use station's mountpoint_id and compare as strings
                                                        $isSelected = (isset($station['mountpoint_id']) && (string)$station['mountpoint_id'] === (string)$mp['id']);
                                                        $displayText = htmlspecialchars($mp['name']) . ' (Masters: ' . htmlspecialchars(implode(', ', $mp['masterStationNames'] ?? [])) . ')';
                                                    ?>
                                                    <option value='<?php echo htmlspecialchars($mountpointValueJson, ENT_QUOTES, 'UTF-8'); ?>' <?php echo $isSelected ? 'selected' : ''; ?>>
                                                        <?php echo $displayText; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        </td>
                                    </tr>
                                    </form>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
require_once PRIVATE_LAYOUTS_PATH . '/admin_footer.php';
?>
