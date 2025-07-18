<?php
// filepath: public/pages/tabs/withdrawals_tab.php
// Tab: Withdrawal Requests
?>
<!-- Export Excel Form -->
<form id="export-withdrawals-form" method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
    <input type="hidden" name="table_name" value="withdrawal_requests">
    <?php
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        echo '<input type="hidden" name="search" value="' . escape_html($_GET['search']) . '">';
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        echo '<input type="hidden" name="status" value="' . escape_html($_GET['status']) . '">';
    }
    ?>
    <div class="bulk-actions-bar">
        <button type="submit" name="export_excel" class="btn btn-success">Xuất Excel</button>
        <button type="submit" name="export_selected_excel" class="btn btn-info" id="export-selected-withdrawals-btn">Xuất mục đã chọn</button>
    </div>
</form>
<form method="GET">
    <input type="hidden" name="tab" value="withdrawals">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm tên người dùng" value="<?php echo escape_html($_GET['search'] ?? ''); ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach(['pending'=>'Chờ xử lý','completed'=>'Hoàn thành','rejected'=>'Từ chối'] as $k=>$v): ?>
                <option value="<?php echo escape_html($k); ?>" <?php echo (($_GET['status']??'')==$k)?'selected':''; ?>><?php echo escape_html($v); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=withdrawals">Xóa</a>
    </div>
</form>


    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-withdrawals"></th>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Số tiền</th>
                    <th>Ngân hàng</th>
                    <th>Số TK</th>
                    <th>Chủ TK</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Ngày yêu cầu</th>
                    <th class="actions text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['items'])): ?>
                    <?php foreach ($data['items'] as $item): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" class="withdrawal-checkbox" value="<?php echo escape_html($item['id']); ?>"></td>
                            <td><?php echo escape_html($item['id']); ?></td>
                            <td><?php echo escape_html($item['username']); ?></td>
                            <td><?php echo format_currency($item['amount']); ?></td>
                            <td><?php echo escape_html($item['bank_name']); ?></td>
                            <td><?php echo escape_html($item['account_number']); ?></td>
                            <td><?php echo escape_html($item['account_holder']); ?></td>
                            <td class="status text-center"><?php echo get_status_badge('withdrawal', $item['status']); ?></td>
                            <td><?php echo format_datetime($item['created_at']); ?></td>
                            <td class="actions">
                                <div class="action-buttons">
                                    <?php if ($item['status'] === 'pending'): ?>
                                        <button class="btn-icon btn-approve" title="Phê duyệt" data-id="<?php echo $item['id']; ?>"><i class="fas fa-check"></i></button>
                                        <button class="btn-icon btn-reject" title="Từ chối" data-id="<?php echo $item['id']; ?>"><i class="fas fa-times"></i></button>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10">Không có dữ liệu.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form> <!-- End Export Excel Form -->

<script src="<?php echo $base_url; ?>public/assets/js/pages/referral/withdrawals.js"></script>

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
