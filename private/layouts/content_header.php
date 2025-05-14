<?php
// Partial: reusable content header with user info
?>
<div class="content-header">
    <h2><?= htmlspecialchars($page_title) ?></h2>
    <div class="user-info">
        <span>Chào mừng, <span class="highlight"><?= htmlspecialchars($user_display_name) ?></span>!</span>
        <a href="<?= $base_path ?>public/pages/setting/profile.php">Hồ sơ</a>
        <a href="<?= $base_path ?>public/pages/auth/admin_logout.php">Đăng xuất</a>
    </div>
</div>
