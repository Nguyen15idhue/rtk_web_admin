<?php
// filepath: public/pages/tabs/commissions_tab.php
// Tab: Commissions
?>
<form method="GET">
    <input type="hidden" name="tab" value="commissions">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm tên người giới thiệu hoặc người được giới thiệu" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach(['pending'=>'Chờ xử lý','approved'=>'Đã duyệt','paid'=>'Đã thanh toán','cancelled'=>'Hủy'] as $k=>$v): ?>
                <option value="<?php echo $k; ?>" <?php echo (($_GET['status']??'')==$k)?'selected':''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=commissions">Xóa</a>
    </div>
</form>

<!-- Export Excel Form -->
<form method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
    <input type="hidden" name="table_name" value="commissions">
    <?php
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        echo '<input type="hidden" name="search" value="' . htmlspecialchars($_GET['search'] ?? '') . '">';
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        echo '<input type="hidden" name="status" value="' . htmlspecialchars($_GET['status'] ?? '') . '">';
    }
    ?>
    <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px; justify-content: flex-end;">
        <button type="submit" name="export_excel" class="btn btn-success">Xuất Excel</button>
        <button type="submit" name="export_selected_excel" class="btn btn-info" id="export-selected-commissions-btn">Xuất mục đã chọn</button>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-commissions"></th>
                    <th>ID</th>
                    <th>Người giới thiệu</th>
                    <th>Người được giới thiệu</th>
                    <th>Mã GD</th>
                    <th>Số tiền</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['items'])): ?>
                    <?php foreach ($data['items'] as $item): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" class="commission-checkbox" value="<?php echo htmlspecialchars($item['id'] ?? ''); ?>"></td>
                            <td><?php echo htmlspecialchars($item['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['referrer_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['referred_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['transaction_id'] ?? ''); ?></td>
                            <td><?php echo format_currency($item['commission_amount']); ?></td>
                            <td class="status text-center"><?php echo get_status_badge('commission', $item['status']); ?></td>
                            <td><?php echo format_datetime($item['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">Không có dữ liệu.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form> <!-- End Export Excel Form -->

<?php if ($data['pages'] > 1): ?>
<?php
$filters_query = [];
if (isset($_GET['search'])) $filters_query['search'] = $_GET['search'];
if (isset($_GET['status'])) $filters_query['status'] = $_GET['status'];
// Ensure 'tab' is always part of the base for pagination links
$filters_query['tab'] = 'commissions';
$pagination_base = '?' . http_build_query(array_filter($filters_query));
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?'); // Added for pagination.php
?>
<?php 
$total_pages = $data['pages'];
$current_page = $data['current'];
// $items_per_page is not available here, but pagination.php has a default.
// $total_items is not available here, but pagination.php can handle this.
include PRIVATE_LAYOUTS_PATH . 'pagination.php'; 
?>
<?php endif; ?>
