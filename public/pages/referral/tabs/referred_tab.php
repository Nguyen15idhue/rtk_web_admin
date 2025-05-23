<?php
// filepath: public/pages/tabs/referred_tab.php
// Tab: Referred Users
?>
<form method="GET">
    <input type="hidden" name="tab" value="referred">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm tên hoặc ID" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-secondary" href="?tab=referred">Xóa</a>
    </div>
</form>
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người giới thiệu</th>
                <th>Người được giới thiệu</th>
                <th>Ngày được giới thiệu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['items'])): ?>
                <?php foreach ($data['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['referrer_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['referred_name'] ?? ''); ?></td>
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
    $pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?');
    $total_pages        = $data['pages'];
    $current_page       = $data['current'];
    $items_per_page     = DEFAULT_ITEMS_PER_PAGE;
    $total_items        = $data['total'];
    ?>
    <?php include PRIVATE_LAYOUTS_PATH . 'pagination.php'; ?>
<?php endif; ?>
