<?php
// filepath: private/actions/invoice/get_revenue_sums.php
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
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});
/**
 * Returns total and successful revenue sums based on filters.
 *
 * @param array $filters Associative array with filter keys like 'date_from', 'date_to', 'status', 'search'.
 * @return array [total_revenue, successful_revenue, pending_revenue, rejected_revenue]
 */
function get_revenue_sums(array $filters = []): array {
    global $db;
    $total = 0.0;
    $success = 0.0;
    $pending = 0.0;
    $rejected = 0.0;
    // Helper to build where clause and params for all applicable filters
    $buildFilters = function() use ($filters): array {
        $where_parts = [];
        $params = [];
        $needs_join = false;
        
        // Date filters
        if (!empty($filters['date_from'])) {
            $where_parts[] = "DATE(th.created_at) >= :df";
            $params[':df'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where_parts[] = "DATE(th.created_at) <= :dt";
            $params[':dt'] = $filters['date_to'];
        }
          // Search filter (search in transaction ID or user email)
        if (!empty($filters['search'])) {
            $search_term = '%' . trim($filters['search']) . '%';
            $where_parts[] = "(CAST(th.id AS CHAR) LIKE :search_th OR u.email LIKE :search_u)";
            $params[':search_th'] = $search_term;
            $params[':search_u'] = $search_term;
            $needs_join = true;
        }
        // Status filter (filter by transaction status)
        if (!empty($filters['status'])) {
            // Map UI status to DB status
            $statusMap = [
                'approved' => 'completed',
                'pending'  => 'pending',
                'rejected' => 'failed',
            ];
            $dbStatus = $statusMap[$filters['status']] ?? $filters['status'];
            $where_parts[] = "th.status = :filter_status";
            $params[':filter_status'] = $dbStatus;
        }
        
        return [
            'where' => !empty($where_parts) ? ' AND ' . implode(' AND ', $where_parts) : '',
            'params' => $params,
            'joins' => $needs_join ? ' LEFT JOIN registration r ON th.registration_id = r.id AND r.deleted_at IS NULL LEFT JOIN user u ON r.user_id = u.id' : '',
            'needs_join' => $needs_join
        ];
    };

    try {
        $filterData = $buildFilters();
        $joins = $filterData['joins'];
        $whereClause = $filterData['where'];
        $params = $filterData['params'];
        
        // Calculate all sums based on date/search filters from buildFilters.
        // The $filters['status'] is intentionally not used here for these aggregate sums.

        // Total revenue (all transactions matching date/search filters)
        $sql_total = "SELECT COALESCE(SUM(th.amount), 0) FROM transaction_history th{$joins} WHERE 1=1{$whereClause}";
        $stmt_total = $db->prepare($sql_total);
        $stmt_total->execute($params);
        $total = (float) $stmt_total->fetchColumn();
        
        // Successful revenue (completed transactions matching date/search filters)
        $sql_success = "SELECT COALESCE(SUM(th.amount), 0) FROM transaction_history th{$joins} WHERE th.status = 'completed'{$whereClause}";
        $stmt_success = $db->prepare($sql_success);
        $stmt_success->execute($params);
        $success = (float) $stmt_success->fetchColumn();
        
        // Pending revenue (pending transactions matching date/search filters)
        $sql_pending = "SELECT COALESCE(SUM(th.amount), 0) FROM transaction_history th{$joins} WHERE th.status = 'pending'{$whereClause}";
        $stmt_pending = $db->prepare($sql_pending);
        $stmt_pending->execute($params);
        $pending = (float) $stmt_pending->fetchColumn();
        
        // Rejected revenue (failed transactions matching date/search filters)
        $sql_rejected = "SELECT COALESCE(SUM(th.amount), 0) FROM transaction_history th{$joins} WHERE th.status = 'failed'{$whereClause}";
        $stmt_rejected = $db->prepare($sql_rejected);
        $stmt_rejected->execute($params);
        $rejected = (float) $stmt_rejected->fetchColumn();
        
        return [$total, $success, $pending, $rejected];

    } catch (PDOException $e) {
        error_log("Database error in get_revenue_sums: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
    } catch (Exception $e) {
        error_log("Error in get_revenue_sums: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
    }

    return [$total, $success, $pending, $rejected];
}
