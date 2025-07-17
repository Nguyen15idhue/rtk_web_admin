<?php
// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];

// --- Authorization Check via Header include ---
require_once $private_layouts_path . 'admin_header.php';
require_once $private_layouts_path . 'admin_sidebar.php';

// --- Page Settings ---
$GLOBALS['required_permission'] = 'station_management'; // Permission requirement
$page_title = "Quản lý Trạm";

// --- Determine active tab ---
$tab_keys = ['station', 'manager', 'mountpoint']; // Valid short tab identifiers
$default_tab_key = 'station';
// Use 'tab' query parameter with short identifiers
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], $tab_keys) ? $_GET['tab'] : $default_tab_key;

// --- Helper function to generate URL with preserved active_tab ---
// Renamed and updated to use 'tab' and short key
function get_url_with_tab($base_page_path, $tab_short_id) {
    $query_params = ['tab' => $tab_short_id];
    // This function assumes $base_page_path is the path component of the URL (e.g., from strtok)
    return $base_page_path . '?' . http_build_query($query_params);
}

// --- Data & Business Logic ---
require_once __DIR__ . '/../../../private/actions/station/management.php';

// --- Permissions for actions ---
$canEditStation = Auth::can('station_management_edit');
?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>    <ul class="custom-tabs-nav">
        <li class="nav-item"><a href="?tab=station" class="nav-link <?php echo $active_tab === 'station' ? 'active' : ''; ?>" data-tab="station">Quản lý trạm</a></li>
        <li class="nav-item"><a href="?tab=manager" class="nav-link <?php echo $active_tab === 'manager' ? 'active' : ''; ?>" data-tab="manager">Quản lý Người quản lý</a></li>
        <li class="nav-item"><a href="?tab=mountpoint" class="nav-link <?php echo $active_tab === 'mountpoint' ? 'active' : ''; ?>" data-tab="mountpoint">Quản lý Mountpoint</a></li>
    </ul>

    <div class="tab-content" id="station" <?php echo $active_tab !== 'station' ? 'style="display:none;"' : ''; ?>>
        <!-- Bulk Export Form -->
        <form id="bulkActionForm" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
            <input type="hidden" name="table_name" value="stations">
            <input type="hidden" name="selected_ids" id="selected_ids_for_export" value="">
            <div class="bulk-actions-bar">
                <button type="submit" name="export_selected_excel" class="btn btn-info">Xuất mục đã chọn</button>
                <button type="submit" name="export_all" class="btn btn-success">Xuất tất cả</button>
                <button type="button" class="btn btn-warning" onclick="window.location.href='<?php echo $base_url; ?>public/handlers/account/index.php?action=cron_update_stations'">
                    <i class="fas fa-sync-alt"></i> Làm mới danh sách
                </button>
                <?php if ($canEditStation && $undefinedStatusCount > 0): ?>
                <button type="button" class="btn btn-danger" onclick="deleteUndefinedStations()" title="Xóa <?php echo $undefinedStatusCount; ?> trạm có trạng thái không xác định">
                    <i class="fas fa-trash-alt"></i> Xóa <?php echo $undefinedStatusCount; ?> trạm không xác định
                </button>
                <?php endif; ?>
            </div>
        </form>
        <!-- Filter Form -->
        <form method="GET" action="" class="filter-bar" style="margin-bottom:15px;">
            <input type="hidden" name="tab" value="station">
            <input type="search" name="q" placeholder="Tìm kiếm trạm..." value="<?php echo htmlspecialchars($filters['q']); ?>">
            <select name="status" class="form-control">
                <option value="">-- Tất cả trạng thái --</option>
                <?php
                // Assuming $station_statuses is passed from the action file
                // Example: $station_statuses = ['0' => 'Stop', '1' => 'Online', '2' => 'No Data', '3' => 'Offline'];
                // In a real scenario, this would come from status_badge_maps.php or a similar source
                $station_status_map = require __DIR__ . '/../../../private/config/status_badge_maps.php';
                $station_statuses = $station_status_map['station'] ?? [];

                foreach ($station_statuses as $key => $status_info):
                    $selected = isset($filters['status']) && (string)$filters['status'] === (string)$key ? 'selected' : '';
                ?>
                    <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($status_info['text']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Tìm</button>
            <a href="<?php echo get_url_with_tab(strtok($_SERVER["REQUEST_URI"], '?'), 'station'); ?>" class="btn btn-secondary">Xóa lọc</a>
        </form>

        <!-- now the table is outside the export form -->
        <div class="table-wrapper">
            <table id="stationsTable" class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Tên Trạm</th>
                        <th>Tên Định danh</th>
                        <th>Người quản lý Hiện tại</th>
                        <th>Mountpoint Hiện tại</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stations)): ?>
                        <tr><td colspan="7">Không tìm thấy trạm nào.</td></tr>
                    <?php else: foreach ($stations as $station): ?>
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" name="ids[]" value="<?php echo htmlspecialchars($station['id']); ?>"></td>
                            <form 
                                id="updateStationForm_<?php echo htmlspecialchars($station['id']); ?>"
                                action="<?php echo BASE_URL; ?>public/handlers/station/index.php" 
                                method="POST"
                            >
                                <input type="hidden" name="action" value="update_station">
                                <input type="hidden" name="station_id" value="<?php echo htmlspecialchars($station['id']); ?>">
                                <td><?php echo htmlspecialchars($station['id']); ?></td>
                                <td><?php echo htmlspecialchars($station['station_name']); ?></td>
                                <td><?php echo htmlspecialchars($station['identificationName'] ?? 'N/A'); ?></td>
                                <td>
                                    <select name="manager_name" class="form-control station-manager-select" 
                                            data-station-id="<?php echo htmlspecialchars($station['id']); ?>"
                                            <?php echo !$canEditStation ? 'disabled' : ''; ?>>
                                        <option value="">-- Chọn người quản lý --</option>
                                        <?php
                                        $currentManagerAssignedName = $station['manager_name'] ?? null;
                                        foreach ($allManagers as $manager):
                                            $managerName = $manager['name'];
                                            $escapedManagerNameForValue = htmlspecialchars($managerName, ENT_QUOTES, 'UTF-8');
                                            $escapedManagerNameForDisplay = htmlspecialchars($managerName);
                                            $isSelected = ($currentManagerAssignedName === $managerName);
                                        ?>
                                            <option value="<?php echo $escapedManagerNameForValue; ?>" <?php if ($isSelected) echo 'selected'; ?>>
                                                <?php echo $escapedManagerNameForDisplay; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="mountpoint_details" class="form-control station-mountpoint-select" 
                                            data-station-id="<?php echo htmlspecialchars($station['id']); ?>"
                                            <?php echo !$canEditStation ? 'disabled' : ''; ?>>
                                        <option value="">-- Chọn Mountpoint --</option>
                                        <?php foreach ($availableMountpoints as $mp): 
                                            $mountpointValueJson = json_encode([
                                                'id'=>$mp['id'],'name'=>$mp['name'],
                                                'masterStationNames'=>$mp['masterStationNames']??[]
                                            ]);
                                            $isSelected = isset($station['mountpoint_id'])
                                                && (string)$station['mountpoint_id']===(string)$mp['id'];
                                            $displayText = htmlspecialchars($mp['name'])
                                                .' (Trạm chủ: '.htmlspecialchars(implode(', ',$mp['masterStationNames']??[])).')';
                                        ?>
                                            <option value='<?php echo htmlspecialchars($mountpointValueJson,ENT_QUOTES,'UTF-8'); ?>'
                                                <?php echo $isSelected?'selected':''; ?>>
                                                <?php echo $displayText; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php 
                                        echo get_status_badge('station', $station['status'] ?? null);
                                    ?>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div> <!-- End of table-wrapper for stations -->
        <?php
            $current_page = $stations_page;
            $total_items = $total_station_items;
            $total_pages = $total_pages_stations;
            $items_per_page = DEFAULT_ITEMS_PER_PAGE;
            // Use new helper function and short tab ID
            $pagination_base_url = get_url_with_tab(strtok($_SERVER["REQUEST_URI"], '?'), 'station');
            $pagination_param = 'station_page';
            include $private_layouts_path . 'pagination.php';
        ?>
    </div> <!-- .tab-content #station -->

    <div class="tab-content" id="manager" 
        <?php echo $active_tab !== 'manager' ? 'style="display:none;"' : ''; ?>>
        <!-- Manager Management Section -->
        <div id="manager-management" class="content-section" style="margin-top:40px;">
            <div class="header-actions">
                <h3>Quản lý Người quản lý</h3>
                <?php if ($canEditStation): ?>
                <button type="button" class="btn btn-primary" onclick="openCreateManagerModal()"><i class="fas fa-plus"></i> Thêm Người quản lý</button>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table class="table" id="managersTable">
                    <thead>
                        <tr>
                            <th>ID</th><th>Tên</th><th>Điện thoại</th><th>Địa chỉ</th><th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allManagers as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['id']); ?></td>
                                <td><?php echo htmlspecialchars($m['name']); ?></td>
                                <td><?php echo htmlspecialchars($m['phone'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($m['address'] ?? ''); ?></td>
                                <td class="actions text-center">
                                    <?php if ($canEditStation): ?>
                                    <button type="button" class="btn-icon btn-edit" title="Sửa" onclick='openEditManagerModal(<?php echo htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8'); ?>)'><i class="fas fa-pencil-alt"></i></button>
                                    <form method="POST" action="<?php echo $base_url; ?>public/handlers/station/manager_index.php" style="display:inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người quản lý này không?');">
                                        <input type="hidden" name="action" value="delete_manager">
                                        <input type="hidden" name="manager_id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="btn-icon btn-secondary" title="Xóa"><i class="fas fa-trash"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
                $current_page = $managers_page;
                $total_items = $total_manager_items;
                $total_pages = $total_pages_managers;
                $items_per_page = DEFAULT_ITEMS_PER_PAGE;
                // Use new helper function and short tab ID
                $pagination_base_url = get_url_with_tab(strtok($_SERVER["REQUEST_URI"], '?'), 'manager');
                $pagination_param = 'manager_page';
                include $private_layouts_path . 'pagination.php';
            ?>        
        </div>
    </div> <!-- .tab-content #manager -->

    <div class="tab-content" id="mountpoint" <?php echo $active_tab !== 'mountpoint' ? 'style="display:none;"' : ''; ?>>
        <div class="content-section" style="margin-top:40px;">
            <div class="header-actions d-flex justify-content-between align-items-center mb-2">
                <h3>Quản lý Mountpoint</h3>
                <div class="sync-buttons">
                    <button type="button" class="btn btn-warning" onclick="window.location.href='<?php echo $base_url; ?>public/handlers/account/index.php?action=cron_update_stations'">
                        <i class="fas fa-sync-alt"></i> Làm mới danh sách
                    </button>
                    <button id="autoUpdateLocationsBtn" class="btn btn-info" data-permission="station_management_edit">
                        <i class="fas fa-map-marker-alt"></i> Tự động cập nhật vị trí
                    </button>
                    <button id="fullSyncMountpointsBtn" class="btn btn-warning" data-permission="station_management_edit">
                        <i class="fas fa-sync-alt"></i> Đồng bộ hoàn toàn
                    </button>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-600 mb-4 description-text">Quản lý các mount point từ hệ thống RTK.</p>
            <!-- Filter Form for Mountpoints -->
            <form method="GET" action="" class="filter-bar" style="margin-bottom:15px;">
                <input type="hidden" name="tab" value="mountpoint">
                <input type="search" name="mp_q" placeholder="Tìm kiếm Mountpoint..." value="<?php echo htmlspecialchars($mp_filters['q_mp'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary">Tìm</button>
                <a href="<?php echo get_url_with_tab(strtok($_SERVER['REQUEST_URI'], '?'), 'mountpoint'); ?>" class="btn btn-secondary">Xóa lọc</a>
            </form>
            <div class="table-wrapper">
                <table class="table" id="mountpointsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mountpoint</th>
                            <th>IP</th>
                            <th>Port</th>
                            <th>Tỉnh/Thành phố</th>
                        </tr>
                    </thead>                    <tbody>
                        <?php if (empty($mountpointsForTable)): ?>
                            <tr><td colspan="5">Không tìm thấy Mountpoint nào.</td></tr>
                        <?php else: foreach ($mountpointsForTable as $mp): 
                            // Map database location info if available
                            $dbMountpoint = null;
                            foreach ($allMountPoints as $dbMp) {
                                if ((string)$dbMp['id'] === (string)$mp['id']) {
                                    $dbMountpoint = $dbMp;
                                    break;
                                }
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mp['id']); ?></td>
                                <td><?php echo htmlspecialchars($mp['name'] ?? $mp['mountpoint'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($mp['ip'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($mp['port'] ?? 'N/A'); ?></td>
                                <td>
                                    <form id="updateMountpointForm_<?php echo htmlspecialchars($mp['id']); ?>" action="<?php echo $base_url; ?>public/handlers/station/mountpoint_index.php" method="POST">
                                        <input type="hidden" name="action" value="update_mountpoint">
                                        <input type="hidden" name="mountpoint_id" value="<?php echo htmlspecialchars($mp['id']); ?>">
                                        <select name="location_id" class="form-control mountpoint-location-select" 
                                                data-mountpoint-id="<?php echo htmlspecialchars($mp['id']); ?>"
                                                <?php echo !$canEditStation ? 'disabled' : ''; ?>>
                                            <option value="">-- Chưa phân bổ --</option>
                                            <?php foreach ($allLocations as $loc): 
                                                $isSelected = $dbMountpoint && ((string)$dbMountpoint['location_id'] === (string)$loc['id']);
                                            ?>
                                                <option value="<?php echo htmlspecialchars($loc['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($loc['province']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <?php
                $current_page = $mountpoints_page;
                $total_items = $total_mountpoint_items;
                $total_pages = $total_pages_mountpoints;
                $items_per_page = DEFAULT_ITEMS_PER_PAGE;
                // Use new helper function and short tab ID
                $pagination_base_url = get_url_with_tab(strtok($_SERVER["REQUEST_URI"], '?'), 'mountpoint');
                $pagination_param = 'mountpoint_page';
                include $private_layouts_path . 'pagination.php';
            ?>
        </div>
    </div>
</main>

<!-- Auto Update Locations Confirmation Modal -->
<div id="autoUpdateLocationsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>🗺️ Tự động cập nhật vị trí Mount Point</h4>
            <span class="modal-close" onclick="window.helpers && window.helpers.closeModal ? window.helpers.closeModal('autoUpdateLocationsModal') : (document.getElementById('autoUpdateLocationsModal').style.display='none')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="info-message" style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <strong>🔍 Cách thức hoạt động:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Hệ thống sẽ lấy dữ liệu <strong>masterStationNames</strong> từ API RTK</li>
                    <li>Lấy <strong>3 ký tự đầu</strong> của mỗi tên trạm chủ</li>
                    <li>So khớp với <strong>province_code</strong> trong bảng location</li>
                    <li>Tự động gán <strong>location_id</strong> tương ứng cho mountpoint</li>
                </ul>
                <p><strong>Ví dụ:</strong> Nếu masterStationNames có "HNI_Station1", sẽ lấy "HNI" để khớp với location có province_code = "HNI"</p>
            </div>
            <div class="warning-message" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <strong>⚠️ Lưu ý:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Chỉ cập nhật các mountpoint <strong>chưa có location_id</strong> hoặc có thể ghi đè</li>
                    <li>Nếu có nhiều trạm chủ, sẽ ưu tiên trạm đầu tiên có khớp province_code</li>
                    <li>Thao tác này <strong>không ảnh hưởng</strong> đến dữ liệu mountpoint hiện tại</li>
                </ul>
            </div>
            <div id="autoUpdateLocationsStatus" style="display: none; margin-top: 15px;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="window.helpers && window.helpers.closeModal ? window.helpers.closeModal('autoUpdateLocationsModal') : (document.getElementById('autoUpdateLocationsModal').style.display='none')">Hủy</button>
            <button id="confirmAutoUpdateLocationsBtn" type="button" class="btn btn-info">
                🗺️ Bắt đầu cập nhật tự động
            </button>
        </div>
    </div>
</div>

<!-- Full Sync Mountpoints Confirmation Modal -->
<div id="fullSyncMountpointsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>⚠️ Đồng bộ hoàn toàn mountpoint từ API RTK</h4>
            <span class="modal-close" onclick="window.helpers && window.helpers.closeModal ? window.helpers.closeModal('fullSyncMountpointsModal') : (document.getElementById('fullSyncMountpointsModal').style.display='none')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="warning-message" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <strong>🚨 CẢNH BÁO:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Thao tác này sẽ <strong>sao lưu dữ liệu hiện tại</strong> vào bảng mountpoint_backup</li>
                    <li>Sau đó <strong>XÓA TOÀN BỘ</strong> dữ liệu trong bảng mount_point</li>
                    <li>Và <strong>GHI ĐÈ</strong> bằng dữ liệu từ API RTK</li>
                    <li>Các thông tin vị trí được phân bổ sẽ được lấy từ backup theo tên mountpoint</li>
                </ul>
            </div>
            <p><strong>Bạn có chắc chắn muốn thực hiện đồng bộ hoàn toàn không?</strong></p>
            <div id="fullSyncMountpointsStatus" style="display: none; margin-top: 15px;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="window.helpers && window.helpers.closeModal ? window.helpers.closeModal('fullSyncMountpointsModal') : (document.getElementById('fullSyncMountpointsModal').style.display='none')">Hủy bỏ</button>
            <button type="button" id="confirmFullSyncMountpointsBtn" class="btn btn-danger">🔄 Xác nhận đồng bộ hoàn toàn</button>
        </div>
    </div>
</div>

<!-- only define basePath inline -->
<script>
    window.basePath = '<?php echo rtrim($base_url, '/'); ?>';
    window.appConfig = {
        permissions: {
            station_management_edit: <?php echo json_encode($canEditStation); ?>
        }
    };
    // window.activeTab will now hold the short tab identifier e.g. 'station'
    window.activeTab = '<?php echo $active_tab; ?>';
</script>

<script>
    window.initialToast = <?php echo (isset($message) && isset($message_type))
        ? json_encode(['message'=>$message,'type'=>$message_type])
        : 'null'; ?>;
</script>

<!-- external scripts -->
<script src="<?php echo $base_url; ?>public/assets/js/pages/station/station_management.js"></script>
<link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/pages/station/station_management.css">

<?php
require_once PRIVATE_LAYOUTS_PATH . '/admin_footer.php';
?>