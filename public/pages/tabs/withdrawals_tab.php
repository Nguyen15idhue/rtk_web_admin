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
<div class="table-responsive">
    <table class="transactions-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Số tiền</th>
                <th>Ngân hàng</th>
                <th>Số TK</th>
                <th>Chủ TK</th>
                <th>Trạng thái</th>
                <th>Ngày yêu cầu</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['items'])): ?>
                <?php foreach ($data['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['username']); ?></td>
                        <td><?php echo htmlspecialchars($item['amount']); ?></td>
                        <td><?php echo htmlspecialchars($item['bank_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['account_number']); ?></td>
                        <td><?php echo htmlspecialchars($item['account_holder']); ?></td>
                        <td><?php echo htmlspecialchars($item['status']); ?></td>
                        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                        <td class="actions">
                            <?php if ($item['status'] === 'pending'): ?>
                                <button class="btn btn-success btn-approve" data-id="<?php echo $item['id']; ?>">Phê duyệt</button>
                                <button class="btn btn-danger btn-reject" data-id="<?php echo $item['id']; ?>">Từ chối</button>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9">Không có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($data['pages'] > 1): ?>
<div class="pagination-footer">
    <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
        <a class="btn <?php echo $i==$data['current']?'active':''; ?>" href="?tab=withdrawals&page=<?php echo $i; ?><?php echo isset($_GET['search'])?'&search='.urlencode($_GET['search']):''; ?><?php echo isset($_GET['status'])?'&status='.urlencode($_GET['status']):''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
