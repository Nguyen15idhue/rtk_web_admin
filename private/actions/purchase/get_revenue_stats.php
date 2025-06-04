<?php
// filepath: private/actions/purchase/get_revenue_stats.php
declare(strict_types=1);

require_once __DIR__ . '/../../utils/functions.php';  

// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden');
}

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('revenue_management_view');
if ($bootstrap === true) {
    $bootstrap = $GLOBALS['__PAGE_BOOTSTRAP_INSTANCE_DATA__'];
}
$db = $bootstrap['db'];

/**
 * Returns detailed revenue statistics for charts and reports
 *
 * @param array $filters Associative array with date and status filters
 * @return array Detailed revenue statistics
 */
function get_revenue_stats(array $filters = []): array {
    global $db;
    
    try {
        // Base query conditions and JOINs
        $base_join = "JOIN registration r ON t.registration_id = r.id JOIN package p ON r.package_id = p.id";
        $conditions = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['date_from'])) {
            $conditions .= " AND DATE(t.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions .= " AND DATE(t.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['package_id'])) {
            $conditions .= " AND r.package_id = :package_id";
            $params[':package_id'] = $filters['package_id'];
        }
        
        // Daily revenue for the last 30 days or specified period
        $daily_query = "
            SELECT 
                DATE(t.created_at) as date,
                COUNT(*) as transaction_count,
                SUM(t.amount) as total_amount,
                SUM(CASE WHEN t.status = 'completed' THEN t.amount ELSE 0 END) as successful_amount,
                SUM(CASE WHEN t.status = 'pending' THEN t.amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN t.status = 'failed' THEN t.amount ELSE 0 END) as failed_amount
            FROM transaction_history t 
            $base_join
            $conditions
            GROUP BY DATE(t.created_at) 
            ORDER BY DATE(t.created_at) DESC 
            LIMIT 30
        ";
        
        $stmt = $db->prepare($daily_query);
        $stmt->execute($params);
        $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Transaction type breakdown
        $type_query = "
            SELECT 
                t.transaction_type,
                COUNT(*) as count,
                SUM(t.amount) as amount
            FROM transaction_history t 
            $base_join
            $conditions
            GROUP BY t.transaction_type
        ";
        
        $stmt = $db->prepare($type_query);
        $stmt->execute($params);
        $type_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Status breakdown
        $status_query = "
            SELECT 
                t.status,
                COUNT(*) as count,
                SUM(t.amount) as amount
            FROM transaction_history t 
            $base_join
            $conditions
            GROUP BY t.status
        ";
        
        $stmt = $db->prepare($status_query);
        $stmt->execute($params);
        $status_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Top packages by revenue
        $package_query = "
            SELECT 
                p.name as package_name,
                COUNT(*) as transaction_count,
                SUM(t.amount) as total_revenue
            FROM transaction_history t
            $base_join
            $conditions
            GROUP BY p.id, p.name
            ORDER BY total_revenue DESC
            LIMIT 10
        ";
        
        $stmt = $db->prepare($package_query);
        $stmt->execute($params);
        $package_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'daily_stats' => $daily_stats,
            'type_breakdown' => $type_breakdown,
            'status_breakdown' => $status_breakdown,
            'package_stats' => $package_stats
        ];
        
    } catch (Exception $e) {
        error_log("Error in get_revenue_stats: " . $e->getMessage());
        return [
            'daily_stats' => [],
            'type_breakdown' => [],
            'status_breakdown' => [],
            'package_stats' => []
        ];
    }
}
