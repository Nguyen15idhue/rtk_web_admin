<?php
// filepath: public/pages/tabs/referrals_tab.php
// Tab: Referrals
?>
<form method="GET">
    <input type="hidden" name="tab" value="referrals">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm mã hoặc tên" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=referrals">Xóa</a>
    </div>
</form>
<div class="table-responsive">
    <table class="transactions-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người tạo</th>
                <th>Mã giới thiệu</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['items'])): ?>
                <?php foreach ($data['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['username']); ?></td>
                        <td><?php echo htmlspecialchars($item['referral_code']); ?></td>
                        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Không có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($data['pages'] > 1): ?>
<div class="pagination-footer">
    <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
        <a class="btn <?php echo $i==$data['current']?'active':''; ?>" href="?tab=referrals&page=<?php echo $i; ?><?php echo isset($_GET['search'])?'&search='.urlencode($_GET['search']):''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
