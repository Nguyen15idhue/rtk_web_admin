<?php
// filepath: private/actions/invoice/get_revenue_sums.php
declare(strict_types=1);

require_once __DIR__ . '/../../utils/functions.php';  // add helper
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized(['admin','customercare']);

// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden');
}

$bootstrap = require __DIR__ . '/../../includes/page_bootstrap.php';
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
        $sql = "SELECT SUM(r.total_price) FROM registration r WHERE r.deleted_at IS NULL "
             . $statusCondition
             . (!empty($filters['date_from']) ? "AND DATE(r.created_at) >= :df " : '')
             . (!empty($filters['date_to']) ? "AND DATE(r.created_at) <= :dt " : '');
        $stmt = $db->prepare($sql);
        if (!empty($filters['date_from'])) {
            $stmt->bindValue(':df', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $stmt->bindValue(':dt', $filters['date_to']);
        }
        if (stripos($statusCondition, 'r.status') !== false) {
            $stmt->bindValue(':status', 'active');
        }
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    };

    try {
        $total = $executeSum();
        $success = $executeSum("AND LOWER(r.status) = :status ");
    } catch (Exception $e) {
        error_log("Error in get_revenue_sums: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());      // <-- Added detailed stack trace
    }

    return [$total, $success];
}
