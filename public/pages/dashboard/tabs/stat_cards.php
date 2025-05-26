<?php
// Stat Cards Tab - Overview
?>
<div class="stats-grid">
    <!-- Card: Người dùng Web -->
    <a href="<?php echo $base_path; ?>public/pages/user/user_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-users" style="color: #3b82f6; background-color: #dbeafe;"></i>
        <div>
            <h3>Người dùng Web</h3>
            <p class="value"><?php echo number_format($total_web_users); ?></p>
        </div>
    </a>
    <!-- Card: TK đã mua gói -->
    <a href="<?php echo $base_path; ?>public/pages/purchase/invoice_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-user-check" style="color: #10b981; background-color: #d1fae5;"></i>
        <div>
            <h3>TK đã mua gói</h3>
            <p class="value"><?php echo number_format($users_with_package); ?></p>
        </div>
    </a>
    <!-- Card: Doanh số (Tháng) -->
    <a href="<?php echo $base_path; ?>public/pages/purchase/revenue_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-dollar-sign" style="color: #f59e0b; background-color: #fef3c7;"></i>
        <div>
            <h3>Doanh số (Tháng)</h3>
            <p class="value"><?php echo format_number_short($monthly_sales); ?></p>
        </div>
    </a>
    <!-- Card: ĐK từ GT -->
    <a href="<?php echo $base_path; ?>public/pages/referral/referral_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-user-tag" style="color: #a855f7; background-color: #f3e8ff;"></i>
        <div>
            <h3>ĐK từ GT</h3>
            <p class="value"><?php echo number_format($referred_registrations); ?></p>
        </div>
    </a>
    <!-- Card: Tổng HH đã trả -->
    <a href="<?php echo $base_path; ?>public/pages/referral/referral_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-coins" style="color: #ec4899; background-color: #fce7f3;"></i>
        <div>
            <h3>Tổng HH đã trả</h3>
            <p class="value"><?php echo format_number_short($total_commission_paid); ?></p>
        </div>
    </a>
    <!-- Card: Voucher đã sử dụng -->
    <a href="<?php echo $base_path; ?>public/pages/voucher/voucher_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-ticket-alt" style="color: #ef4444; background-color: #fee2e2;"></i>
        <div>
            <h3>Voucher đã sử dụng</h3>
            <p class="value"><?php echo number_format($used_vouchers); ?> / <?php echo number_format($total_vouchers); ?></p>
        </div>
    </a>
    <!-- Card: Yêu cầu chờ xử lý -->
    <a href="<?php echo $base_path; ?>public/pages/support/support_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-hourglass-half" style="color: #eab308; background-color: #fef9c3;"></i>
        <div>
            <h3>Yêu cầu chờ xử lý</h3>
            <p class="value"><?php echo number_format($pending_support_requests); ?></p>
        </div>
    </a>
    <!-- Card: Trạm không hoạt động -->
    <a href="<?php echo $base_path; ?>public/pages/station/station_management.php" class="stat-card" style="text-decoration:none;">
        <i class="icon fas fa-broadcast-tower text-muted" style="color: #ef4444; background-color: #fee2e2;"></i>
        <div>
            <h3>Trạm không hoạt động</h3>
            <p class="value"><?php echo number_format($inactive_stations); ?></p>
        </div>
    </a>
</div>
