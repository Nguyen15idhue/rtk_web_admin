<?php
// filepath: public/pages/tabs/commissions_tab.php
// Tab: Commissions
?>
<!-- Export Excel Form -->
<form id="export-commissions-form" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
    <input type="hidden" name="table_name" value="commissions">
    <?php
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        echo '<input type="hidden" name="search" value="' . htmlspecialchars($_GET['search'] ?? '') . '">';
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        echo '<input type="hidden" name="status" value="' . htmlspecialchars($_GET['status'] ?? '') . '">';
    }
    ?>
    <div class="bulk-actions-bar">
        <button type="submit" name="export_excel" class="btn btn-success">Xuất Excel</button>
        <button type="submit" name="export_selected_excel" class="btn btn-info" id="export-selected-commissions-btn">Xuất mục đã chọn</button>
    </div>
</form>
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

<script src="<?php echo $base_url; ?>public/assets/js/pages/referral/commissions.js"></script>

<?php if ($data['pages'] > 1): ?>
    <?php
    // mirror voucher pagination setup
    $pagination_base_url = $_SERVER["REQUEST_URI"];
    $total_pages        = $data['pages'];
    $current_page       = $data['current'];
    $items_per_page     = DEFAULT_ITEMS_PER_PAGE;
    $total_items        = $data['total'];
    ?>
    <?php include PRIVATE_LAYOUTS_PATH . 'pagination.php'; ?>
<?php endif; ?>
