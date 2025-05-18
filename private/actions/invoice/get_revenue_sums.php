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
 * @param array $filters Associative array with 'date_from' and 'date_to' keys.
 * @return array [total_revenue, successful_revenue]
 */
function get_revenue_sums(array $filters = []): array {
    global $db;
    $total = 0.0;
    $success = 0.0;

    // Helper to prepare and execute a sum query
    $executeSum = function(string $statusCondition = '') use ($db, $filters): float {
        $sql = "SELECT SUM(t.amount) FROM transaction_history t WHERE 1=1 "
             . $statusCondition
             . (!empty($filters['date_from']) ? "AND DATE(t.created_at) >= :df " : '')
             . (!empty($filters['date_to'])   ? "AND DATE(t.created_at) <= :dt " : '');
        $stmt = $db->prepare($sql);
        if (!empty($filters['date_from'])) {
            $stmt->bindValue(':df', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $stmt->bindValue(':dt', $filters['date_to']);
        }
        if (stripos($statusCondition, 't.status') !== false) {
            $stmt->bindValue(':status', 'completed');
        }
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    };

    try {
        $total   = $executeSum();                               // tổng tất cả giao dịch
        $success = $executeSum("AND t.status = :status ");      // chỉ giao dịch completed
    } catch (Exception $e) {
        error_log("Error in get_revenue_sums: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
    }

    return [$total, $success];
}
