<?php
// Stat Cards Tab - Overview
?>
<div class="stats-grid">
    <!-- Card: Người dùng Web -->
    <div class="stat-card">
        <i class="icon fas fa-users" style="color: #3b82f6; background-color: #dbeafe;"></i>
        <div>
            <h3>Người dùng Web</h3>
            <p class="value"><?php echo number_format($total_web_users); ?></p>
        </div>
    </div>
    <!-- Card: TK đã mua gói -->
    <div class="stat-card">
        <i class="icon fas fa-user-check" style="color: #10b981; background-color: #d1fae5;"></i>
        <div>
            <h3>TK đã mua gói</h3>
            <p class="value"><?php echo number_format($users_with_package); ?></p>
        </div>
    </div>
    <!-- Card: TK đo đạc HĐ -->
    <div class="stat-card">
        <i class="icon fas fa-ruler-combined" style="color: #6366f1; background-color: #e0e7ff;"></i>
        <div>
            <h3>TK đo đạc HĐ</h3>
            <p class="value"><?php echo number_format($active_survey_accounts); ?></p>
        </div>
    </div>
    <!-- Card: Doanh số (Tháng) -->
    <div class="stat-card">
        <i class="icon fas fa-dollar-sign" style="color: #f59e0b; background-color: #fef3c7;"></i>
        <div>
            <h3>Doanh số (Tháng)</h3>
            <p class="value"><?php echo format_number_short($monthly_sales); ?></p>
        </div>
    </div>
    <!-- Card: Người giới thiệu -->
    <div class="stat-card">
        <i class="icon fas fa-user-group" style="color: #4f46e5; background-color: #e0e7ff;"></i>
        <div>
            <h3>Người giới thiệu</h3>
            <p class="value"><?php echo number_format($total_referrers); ?></p>
        </div>
    </div>
    <!-- Card: ĐK từ GT -->
    <div class="stat-card">
        <i class="icon fas fa-user-tag" style="color: #a855f7; background-color: #f3e8ff;"></i>
        <div>
            <h3>ĐK từ GT</h3>
            <p class="value"><?php echo number_format($referred_registrations); ?></p>
        </div>
    </div>
    <!-- Card: Tổng HH đã trả -->
    <div class="stat-card">
        <i class="icon fas fa-coins" style="color: #ec4899; background-color: #fce7f3;"></i>
        <div>
            <h3>Tổng HH đã trả</h3>
            <p class="value"><?php echo format_number_short($total_commission_paid); ?></p>
        </div>
    </div>
</div>
