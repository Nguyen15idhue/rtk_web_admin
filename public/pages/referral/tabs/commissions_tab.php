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
<div class="table-responsive">
    <table class="transactions-table">
        <thead>
            <tr>
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
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['referrer_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['referred_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['transaction_id']); ?></td>
                        <td><?php echo format_currency($item['commission_amount']); ?></td>
                        <td class="status text-center"><?php echo get_commission_status_badge($item['status']); ?></td>
                        <td><?php echo format_datetime($item['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">Không có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($data['pages'] > 1): ?>
<div class="pagination-footer">
    <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
        <a class="btn <?php echo $i==$data['current']?'active':''; ?>" href="?tab=commissions&page=<?php echo $i; ?><?php echo isset($_GET['search'])?'&search='.urlencode($_GET['search']):''; ?><?php echo isset($_GET['status'])?'&status='.urlencode($_GET['status']):''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
