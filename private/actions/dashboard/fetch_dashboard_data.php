<?php
// filepath: private\actions\dashboard\fetch_dashboard_data.php

$config = require __DIR__ . '/../../core/page_bootstrap.php';

// Use redirect-based auth check to ensure page access and redirect to login if unauthenticated
require_once __DIR__ . '/../../core/auth_check.php';


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
        'recent_activities'            => [],        'voucher_details_map'          => [], // Added for pre-fetched voucher codes
        'total_vouchers'               => 0, // Added for voucher stats
        'used_vouchers'                => 0, // Added for voucher stats
        'pending_support_requests'     => 0, // Added default for support stats
        'inactive_stations'            => 0, // Added default for station stats
        'top_users'                    => [], // Added array placeholder for top users ranking
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
        
        // Doanh số tháng - Optimized
        $current_month_start = date('Y-m-01 00:00:00');
        $current_month_end = date('Y-m-t 23:59:59'); // 't' gives the last day of the month

        $stmt = $pdo->prepare(
            "SELECT IFNULL(SUM(amount),0) FROM transaction_history
             WHERE status='completed' 
               AND transaction_type IN ('purchase','renewal')
               AND created_at >= :start_of_month
               AND created_at <= :end_of_month"
        );
        $stmt->execute([
            ':start_of_month' => $current_month_start,
            ':end_of_month' => $current_month_end
        ]);
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

        // Thống kê Voucher
        // Tổng số voucher đã tạo
        $dashboard_data['total_vouchers'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM voucher"
        )->fetchColumn();
        
        // Số voucher đã được sử dụng (đếm các voucher_id duy nhất trong transaction_history)
        $dashboard_data['used_vouchers'] = (int)$pdo->query(
            "SELECT COUNT(DISTINCT voucher_id) FROM transaction_history WHERE voucher_id IS NOT NULL"
        )->fetchColumn();

        // Hoạt động gần đây (chỉ các action do khách hàng tạo)
        $dashboard_data['recent_activities'] = $pdo->query(
            "SELECT al.*, u.username AS actor_name
             FROM activity_logs al
             LEFT JOIN `user` u ON al.user_id = u.id
             WHERE al.user_id IS NOT NULL
               AND al.action IN (
                 'purchase',
                 'create_support_request',
                 'request_invoice',
                 'renewal_request',
                 'withdrawal_request'
               )
             ORDER BY al.created_at DESC
             LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);
        
        // Pre-fetch voucher codes for recent purchase/renewal activities
        $registration_ids_for_vouchers = [];
        foreach ($dashboard_data['recent_activities'] as $activity) {
            if (in_array($activity['action'], ['purchase', 'renewal_request'])) {
                $details = json_decode($activity['new_values'] ?? '', true);
                if (!empty($details['registration_id'])) {
                    $registration_ids_for_vouchers[] = $details['registration_id'];
                }
            }
        }

        if (!empty($registration_ids_for_vouchers)) {
            $placeholders = implode(',', array_fill(0, count($registration_ids_for_vouchers), '?'));
            $sql_vouchers = "
                SELECT th.registration_id, v.code 
                FROM transaction_history th
                JOIN voucher v ON th.voucher_id = v.id
                WHERE th.registration_id IN ({$placeholders})
                  AND th.voucher_id IS NOT NULL
            "; // We only care about transactions that actually have a voucher
            $stmt_vouchers = $pdo->prepare($sql_vouchers);
            $stmt_vouchers->execute($registration_ids_for_vouchers);
            $voucher_rows = $stmt_vouchers->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($voucher_rows as $row) {
                $dashboard_data['voucher_details_map'][$row['registration_id']] = htmlspecialchars($row['code']);
            }
        }
        
        // Hỗ trợ: số yêu cầu chờ xử lý
        $dashboard_data['pending_support_requests'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM support_requests WHERE status IN ('pending','in_progress')"
        )->fetchColumn();        // Trạm: số trạm không hoạt động
        $dashboard_data['inactive_stations'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM station WHERE status IN (0,2,3)"
        )->fetchColumn();

        // Top users by total spending and commission
        $stmt_top_users = $pdo->query(
            "SELECT u.id, u.username AS full_name, u.email,
                    COALESCE(SUM(th.amount), 0) AS total_spent,
                    COALESCE(rc_sum.total_commission, 0) AS total_commission_earned,
                    (COALESCE(SUM(th.amount), 0) + COALESCE(rc_sum.total_commission, 0)) AS total_score
             FROM `user` u
             LEFT JOIN transaction_history th ON u.id = th.user_id AND th.status = 'completed' AND th.transaction_type IN ('purchase','renewal')
             LEFT JOIN (
                 SELECT referrer_id, SUM(commission_amount) AS total_commission
                 FROM referral_commission
                 WHERE status = 'approved'
                 GROUP BY referrer_id
             ) rc_sum ON u.id = rc_sum.referrer_id
             WHERE u.status = 1
             GROUP BY u.id, u.username, u.email
             ORDER BY total_score DESC
             LIMIT 10"
        );
        $dashboard_data['top_users'] = $stmt_top_users->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        error_log("Dashboard Error: " . $e->getMessage());
    }
    return $dashboard_data;
}

?>