<?php
// filepath: public/pages/referral_management.php
// --- Bootstrap and Initialization ---
$bootstrap_data = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
$page_title = 'Quản lý Giới thiệu';
$db                  = $bootstrap_data['db'];
$base_url            = $bootstrap_data['base_url'];
$private_layouts_path= $bootstrap_data['private_layouts_path'];
$private_actions_path= $bootstrap_data['private_actions_path'];
$user_display_name   = $bootstrap_data['user_display_name'];
$admin_role          = $bootstrap_data['admin_role'];

// Authorization: ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . $base_url . 'public/pages/auth/admin_login.php');
    exit;
}

// Determine current tab
$tabs = ['referrals','commissions','referred','withdrawals'];
$current_tab = isset($_GET['tab']) && in_array($_GET['tab'], $tabs) ? $_GET['tab'] : 'referrals';

// Include fetch logic for each tab
require_once $private_actions_path . 'referral/fetch_referrals.php';
require_once $private_actions_path . 'referral/fetch_commissions.php';
require_once $private_actions_path . 'referral/fetch_referred_users.php';
require_once $private_actions_path . 'referral/fetch_withdrawal_requests.php';

// Fetch data
$data = [];
switch ($current_tab) {
    case 'commissions':
        $data = fetch_paginated_commissions($db, $_GET);
        break;
    case 'referred':
        $data = fetch_paginated_referred_users($db, $_GET);
        break;
    case 'withdrawals':
        $data = fetch_paginated_withdrawals($db, $_GET);
        break;
    case 'referrals':
    default:
        $data = fetch_paginated_referrals($db, $_GET);
        break;
}

// Include layouts
include $private_layouts_path . 'admin_header.php';
include $private_layouts_path . 'admin_sidebar.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Đăng Nhập</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/pages/referral_management.css">
</head>
<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>
    <div class="content-section">
        <ul class="custom-tabs-nav">
            <li class="nav-item"><a class="nav-link <?php echo $current_tab=='referrals'?'active':''?>" href="?tab=referrals">Mã Giới Thiệu</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_tab=='commissions'?'active':''?>" href="?tab=commissions">Hoa Hồng</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_tab=='referred'?'active':''?>" href="?tab=referred">Người Được Giới Thiệu</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $current_tab=='withdrawals'?'active':''?>" href="?tab=withdrawals">Yêu Cầu Rút Tiền</a></li>
        </ul>
        <div class="tab-content" style="margin-top:20px;">
            <?php if ($current_tab=='referrals'): ?>
                <?php include __DIR__ . '/tabs/referrals_tab.php'; ?>
            <?php elseif ($current_tab=='commissions'): ?>
                <?php include __DIR__ . '/tabs/commissions_tab.php'; ?>
            <?php elseif ($current_tab=='referred'): ?>
                <?php include __DIR__ . '/tabs/referred_tab.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . '/tabs/withdrawals_tab.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
<script>
    window.basePath = '<?php echo rtrim($base_url, '/'); ?>';
</script>
<script defer src="<?php echo $base_url; ?>public/assets/js/pages/referral/referral_management.js"></script>
