<?php
// filepath: public/pages/tabs/withdrawals_tab.php
// Tab: Withdrawal Requests
?>
<form method="GET">
    <input type="hidden" name="tab" value="withdrawals">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm tên người dùng" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach(['pending'=>'Chờ xử lý','completed'=>'Hoàn thành','rejected'=>'Từ chối'] as $k=>$v): ?>
                <option value="<?php echo $k; ?>" <?php echo (($_GET['status']??'')==$k)?'selected':''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=withdrawals">Xóa</a>
    </div>
</form>

<!-- Export Excel Form -->
<form method="POST" action="<?php echo $base_url; ?>public/handlers/excel_index.php">
    <input type="hidden" name="table_name" value="withdrawal_requests">
    <?php
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        echo '<input type="hidden" name="search" value="' . htmlspecialchars($_GET['search']) . '">';
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        echo '<input type="hidden" name="status" value="' . htmlspecialchars($_GET['status']) . '">';
    }
    ?>
    <div class="bulk-actions-bar" style="margin-bottom:15px; display:flex; gap:10px; justify-content: flex-end;">
        <button type="submit" name="export_excel" class="btn btn-success">Xuất Excel</button>
        <button type="submit" name="export_selected_excel" class="btn btn-info" id="export-selected-withdrawals-btn">Xuất mục đã chọn</button>
    </div>

    <div class="transactions-table-wrapper">
        <table class="transactions-table">
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
                            <td><input type="checkbox" name="selected_ids[]" class="withdrawal-checkbox" value="<?php echo htmlspecialchars($item['id']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['username']); ?></td>
                            <td><?php echo format_currency($item['amount']); ?></td>
                            <td><?php echo htmlspecialchars($item['bank_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['account_number']); ?></td>
                            <td><?php echo htmlspecialchars($item['account_holder']); ?></td>
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

<?php if ($data['pages'] > 1): ?>
<?php
$filters_query = [];
if (isset($_GET['search'])) $filters_query['search'] = $_GET['search'];
if (isset($_GET['status'])) $filters_query['status'] = $_GET['status'];
$filters_query['tab'] = 'withdrawals'; 
$pagination_base = '?' . http_build_query(array_filter($filters_query));
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?'); // Added for pagination.php
?>
<?php 
$total_pages = $data['pages'];
$current_page = $data['current'];
include PRIVATE_LAYOUTS_PATH . 'pagination.php'; 
?>
<?php endif; ?>
