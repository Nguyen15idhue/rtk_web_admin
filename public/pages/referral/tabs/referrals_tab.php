<?php
// filepath: public/pages/tabs/referrals_tab.php
// Tab: Referrals
?>
<form method="GET">
    <input type="hidden" name="tab" value="referrals">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm mã hoặc tên" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=referrals">Xóa</a>
    </div>
</form>
<div class="table-wrapper">
    <table class="table">
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
                        <td><?php echo htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($item['referral_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo format_datetime($item['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Không có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
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
