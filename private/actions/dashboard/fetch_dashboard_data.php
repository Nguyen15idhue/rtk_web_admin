<?php
// filepath: private\actions\dashboard\fetch_dashboard_data.php

$config = require __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthenticated();
$pdo = $config['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$pdo) {
    $pdo = null;
});

/**
 * Fetches all necessary data for the admin dashboard.
 *
 * @return array An associative array containing all dashboard data.
 */
function fetch_dashboard_data(): array
{
    global $pdo;
    $dashboard_data = [
        'total_web_users'              => 0,
        'users_with_package'           => 0,
        'active_survey_accounts'       => 0,
        'monthly_sales'                => 0,
        'total_referrers'              => 0,
        'referred_registrations'       => 0,
        'total_commission_paid'        => 0,
        'recent_activities'            => [],
        'new_registrations_chart_data' => ['labels'=>[], 'data'=>[]],
        'referral_chart_data'          => ['labels'=>[], 'data'=>[]],
    ];
    try {
        // Tổng người dùng web
        $dashboard_data['total_web_users'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM `user` WHERE `status`=1"
        )->fetchColumn();
        // Người dùng đã mua gói
        $dashboard_data['users_with_package'] = (int)$pdo->query(
            "SELECT COUNT(DISTINCT user_id) FROM registration WHERE package_id IS NOT NULL AND status='active'"
        )->fetchColumn();
        // TK đo đạc HĐ
        $dashboard_data['active_survey_accounts'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM survey_account WHERE enabled=1"
        )->fetchColumn();
        // Doanh số tháng
        $stmt = $pdo->prepare(
            "SELECT IFNULL(SUM(amount),0) FROM transaction_history
             WHERE status='completed' 
               AND transaction_type IN ('purchase','renewal')
               AND MONTH(created_at)=MONTH(CURRENT_DATE())
               AND YEAR(created_at)=YEAR(CURRENT_DATE())"
        );
        $stmt->execute();
        $dashboard_data['monthly_sales'] = (float)$stmt->fetchColumn();
        // Người giới thiệu
        $dashboard_data['total_referrers'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM referral"
        )->fetchColumn();
        // ĐK từ GT
        $dashboard_data['referred_registrations'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM referred_user"
        )->fetchColumn();
        // Tổng HH đã trả (approved)
        $dashboard_data['total_commission_paid'] = (float)$pdo->query(
            "SELECT IFNULL(SUM(commission_amount),0) FROM referral_commission WHERE status='approved'"
        )->fetchColumn();
        // Hoạt động gần đây
        $dashboard_data['recent_activities'] = $pdo->query(
            "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);
        // Biểu đồ ĐK mới 7 ngày
        $stmt = $pdo->prepare(
            "SELECT DATE(created_at) AS d, COUNT(*) AS c 
             FROM registration 
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY d ORDER BY d"
        );
        $stmt->execute();
        $labels=[]; $data=[];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[] = $r['d'];
            $data[]   = (int)$r['c'];
        }
        $dashboard_data['new_registrations_chart_data'] = ['labels'=>$labels,'data'=>$data];
        // Biểu đồ GT 7 ngày
        $stmt = $pdo->prepare(
            "SELECT DATE(created_at) AS d, COUNT(*) AS c 
             FROM referral 
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY d ORDER BY d"
        );
        $stmt->execute();
        $labels=[]; $data=[];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $labels[] = $r['d'];
            $data[]   = (int)$r['c'];
        }
        $dashboard_data['referral_chart_data'] = ['labels'=>$labels,'data'=>$data];
    } catch (\Exception $e) {
        error_log("Dashboard Error: " . $e->getMessage());
    }
    return $dashboard_data;
}

?>