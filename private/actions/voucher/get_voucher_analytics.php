<?php
// File: private/actions/voucher/get_voucher_analytics.php
// Encapsulates voucher analytics logic for reuse in public pages or handlers.

require_once BASE_PATH . '/utils/functions.php';
require_once BASE_PATH . '/classes/VoucherModel.php';

/**
 * Retrieves various voucher analytics based on optional date filters.
 *
 * @param string|null $start_date Format 'YYYY-MM-DD'
 * @param string|null $end_date   Format 'YYYY-MM-DD'
 * @return array                   Analytics data.
 */
function get_voucher_analytics($start_date = null, $end_date = null) {
    global $db;
    $analytics = [];
    $conditions = [];
    $params = [];

    if ($start_date) {
        $conditions[] = 'created_at >= ?';
        $params[] = $start_date . ' 00:00:00';
    }
    if ($end_date) {
        $conditions[] = 'created_at <= ?';
        $params[] = $end_date . ' 23:59:59';
    }
    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    // Overview
    $sql = "SELECT 
            COUNT(*) as total_vouchers,
            COALESCE(SUM(CASE WHEN is_active = 1 AND end_date >= NOW() THEN 1 ELSE 0 END), 0) as active_vouchers,
            COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) as inactive_vouchers,
            COALESCE(SUM(CASE WHEN end_date < NOW() THEN 1 ELSE 0 END), 0) as expired_vouchers,
            COALESCE(SUM(used_quantity), 0) as total_usage,
            COALESCE(SUM(quantity), 0) as total_available
        FROM voucher " . $where;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $analytics['overview'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Top vouchers
    $sql2 = "SELECT code, description, voucher_type, used_quantity, quantity,
               ROUND((used_quantity * 100.0 / NULLIF(quantity, 0)), 2) as usage_rate
        FROM voucher " . $where . "
        ORDER BY used_quantity DESC 
        LIMIT 10";
    $stmt = $db->prepare($sql2);
    $stmt->execute($params);
    $analytics['top_vouchers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // By type
    $sql3 = "SELECT voucher_type,
            COUNT(*) as count,
            SUM(used_quantity) as total_used,
            AVG(used_quantity) as avg_used
        FROM voucher " . $where . "
        GROUP BY voucher_type";
    $stmt = $db->prepare($sql3);
    $stmt->execute($params);
    $analytics['by_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Expiring soon (next 7 days)
    $stmt = $db->prepare("SELECT code, description, end_date, used_quantity, quantity
        FROM voucher 
        WHERE end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        AND is_active = 1
        ORDER BY end_date ASC");
    $stmt->execute();
    $analytics['expiring_soon'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly usage (last 3 months)
    $stmt = $db->prepare("SELECT 
            DATE_FORMAT(uvu.used_at, '%Y-%m') as month,
            COUNT(*) as usage_count
        FROM user_voucher_usage uvu
        JOIN voucher v ON uvu.voucher_id = v.id
        WHERE uvu.used_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
        GROUP BY DATE_FORMAT(uvu.used_at, '%Y-%m')
        ORDER BY month DESC");
    $stmt->execute();
    $analytics['monthly_usage'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $analytics;
}
