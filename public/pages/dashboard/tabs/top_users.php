<?php
// filepath: public/pages/dashboard/tabs/top_users.php
// Top Users Tab - Overview
?>
<section class="content-section">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top Người Dùng</h3>
        </div>
        <div class="card-body p-0">
            <?php $maxUsers = 5; $totalUsers = count($top_users); ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên Người Dùng</th>
                            <th>Email</th>
                            <th>Tổng Chi Tiêu</th>
                            <th>Tổng Hoa Hồng</th>
                            <th>Điểm Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_users)) : ?>
                            <?php foreach ($top_users as $index => $user) : ?>
                                <?php $num = $index + 1; ?>
                                <?php
                                $row_class = '';
                                if ($index < 3) {
                                    $row_class .= ' top-user-highlight';
                                }
                                if ($index >= $maxUsers) {
                                    $row_class .= ' more-users hidden';
                                }
                                ?>
                                <tr class="<?php echo trim($row_class); ?>">
                                    <td>
                                        <?php if ($index === 0): ?>
                                            <span class="medal-icon">🥇</span>
                                        <?php elseif ($index === 1): ?>
                                            <span class="medal-icon">🥈</span>
                                        <?php elseif ($index === 2): ?>
                                            <span class="medal-icon">🥉</span>
                                        <?php else: ?>
                                            <?php echo $num; ?>.
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo number_format($user['total_spent']); ?></span></td>
                                    <td><span class="badge bg-info"><?php echo number_format($user['total_commission_earned']); ?></span></td>
                                    <td><span class="badge bg-success"><?php echo number_format($user['total_score']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalUsers > $maxUsers): ?>
                <div class="text-center my-2">
                    <button id="toggle-users" class="btn btn-sm btn-secondary">Xem thêm</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php if ($totalUsers > $maxUsers): ?>
<script>
document.getElementById('toggle-users').addEventListener('click', function() {
    const rows = document.querySelectorAll('tr.more-users');
    rows.forEach(r => r.classList.toggle('hidden'));
    this.textContent = this.textContent === 'Xem thêm' ? 'Thu gọn' : 'Xem thêm';
});
</script>
<?php endif; ?>
