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
<div class="transactions-table-wrapper">
    <table class="transactions-table">
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
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['referrer_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['referred_name']); ?></td>
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
<?php
$filters_query = [];
if (isset($_GET['search'])) $filters_query['search'] = $_GET['search'];
// Ensure 'tab' is always part of the base for pagination links
$filters_query['tab'] = 'referred';
$pagination_base = '?' . http_build_query(array_filter($filters_query));
$pagination_base_url = strtok($_SERVER["REQUEST_URI"], '?'); // Added for pagination.php
?>
<?php 
$total_pages = $data['pages'];
$current_page = $data['current'];
include PRIVATE_LAYOUTS_PATH . 'pagination.php'; 
?>
<?php endif; ?>
