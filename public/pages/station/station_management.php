<?php
require __DIR__ . '/../../../private/actions/station/management.php';

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
    </form>

    <!-- now the table is outside the export form -->
    <div class="transactions-table-wrapper">
        <table id="stationsTable" class="transactions-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Tên Trạm</th>
                    <th>Tên Định danh</th>
                    <th>Người quản lý Hiện tại</th>
                    <th>Mountpoint Hiện tại</th>
                    <th>Hành động</th>
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
                                <input list="manager_names_<?php echo $station['id']; ?>" name="manager_name"
                                       class="form-control" value="<?php echo htmlspecialchars($station['manager_name'] ?? ''); ?>"
                                       placeholder="Nhập tên người quản lý">
                                <datalist id="manager_names_<?php echo $station['id']; ?>">
                                    <?php foreach ($allManagers as $manager): ?>
                                        <option value="<?php echo htmlspecialchars($manager['name']); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </td>
                            <td>
                                <?php
                                // find and display current mountpoint info
                                $currentMp = null;
                                foreach ($availableMountpoints as $mp) {
                                    if ((string)$mp['id'] === (string)$station['mountpoint_id']) {
                                        $currentMp = $mp;
                                        break;
                                    }
                                }
                                if ($currentMp) {
                                    echo htmlspecialchars($currentMp['name'])
                                       . ' (Trạm chủ: '
                                       . htmlspecialchars(implode(', ', $currentMp['masterStationNames'] ?? []))
                                       . ')';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-primary btn-sm"
                                    onclick="document.getElementById('updateStationForm_<?php echo htmlspecialchars($station['id']); ?>').submit();"
                                >
                                    Lưu
                                </button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Manager Management Section -->
    <div id="manager-management" class="content-section" style="margin-top:40px;">
        <div class="header-actions">
            <h3>Quản lý Người quản lý</h3>
            <button type="button" class="btn btn-primary" onclick="openCreateManagerModal()"><i class="fas fa-plus"></i> Thêm Người quản lý</button>
        </div>
        <div class="transactions-table-wrapper">
            <table class="transactions-table" id="managersTable">
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
                                <button type="button" class="btn-icon btn-edit" title="Sửa" onclick='openEditManagerModal(<?php echo htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8'); ?>)'><i class="fas fa-pencil-alt"></i></button>
                                <form method="POST" action="<?php echo $base_url; ?>public/handlers/station/manager_index.php" style="display:inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người quản lý này không?');">
                                    <input type="hidden" name="action" value="delete_manager">
                                    <input type="hidden" name="manager_id" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="btn-icon btn-secondary" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Manager Modal -->
    <div id="createManagerModal" class="modal">
        <div class="modal-content">
            <form id="createManagerForm" method="POST" action="<?php echo $base_url; ?>public/handlers/station/manager_index.php">
                <div class="modal-header">
                    <h4>Thêm Người quản lý</h4>
                    <span class="modal-close" onclick="closeManagerModal('createManagerModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_manager">
                    <div class="form-group"><label>Tên</label><input type="text" class="form-control" name="name" required></div>
                    <div class="form-group"><label>Điện thoại</label><input type="text" class="form-control" name="phone"></div>
                    <div class="form-group"><label>Địa chỉ</label><input type="text" class="form-control" name="address"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeManagerModal('createManagerModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Manager Modal -->
    <div id="editManagerModal" class="modal">
        <div class="modal-content">
            <form id="editManagerForm" method="POST" action="<?php echo $base_url; ?>public/handlers/station/manager_index.php">
                <div class="modal-header">
                    <h4>Sửa Người quản lý</h4>
                    <span class="modal-close" onclick="closeManagerModal('editManagerModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_manager">
                    <input type="hidden" id="editManagerId" name="manager_id">
                    <div class="form-group"><label>Tên</label><input type="text" class="form-control" id="editManagerName" name="name" required></div>
                    <div class="form-group"><label>Điện thoại</label><input type="text" class="form-control" id="editManagerPhone" name="phone"></div>
                    <div class="form-group"><label>Địa chỉ</label><input type="text" class="form-control" id="editManagerAddress" name="address"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeManagerModal('editManagerModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

</main>

<?php
require_once PRIVATE_LAYOUTS_PATH . '/admin_footer.php';
?>

<script src="<?php echo $base_url; ?>public/assets/js/pages/station/station_management.js"></script>
