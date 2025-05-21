<?php
// filepath: public/pages/tabs/referrals_tab.php
// Tab: Referrals
?>
<form method="GET">
    <input type="hidden" name="tab" value="referrals">
    <div class="filter-bar">
        <input type="search" name="search" placeholder="Tìm mã hoặc tên" value="<?php echo htmlspecialchars($_GET['search'] ?? null); ?>">
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
                        <td><?php echo htmlspecialchars($item['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['username'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['referral_code'] ?? ''); ?></td>
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
$filters_query = [];
if (isset($_GET['search'])) $filters_query['search'] = $_GET['search'];
// Ensure 'tab' is always part of the base for pagination links
$filters_query['tab'] = 'referrals';
$pagination_base = '?' . http_build_query(array_filter($filters_query));
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?'); // Added for pagination.php
?>
<?php 
$total_pages = $data['pages'];
$current_page = $data['current'];
include PRIVATE_LAYOUTS_PATH . 'pagination.php'; 
?>
<?php endif; ?>
