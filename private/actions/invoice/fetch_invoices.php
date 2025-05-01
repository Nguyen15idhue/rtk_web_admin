<?php
declare(strict_types=1);
// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    die('Forbidden');
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';

/**
 * Fetches invoice requests for admin review with filtering and pagination.
 *
 * @param array $filters ['status']
 * @param int $page
 * @param int $per_page
 * @return array ['invoices', 'total_count', 'current_page', 'per_page', 'total_pages']
 */
function fetch_admin_invoices(array $filters = [], int $page = 1, int $per_page = 10): array {
    $db = Database::getInstance()->getConnection();
    if (!$db) {
        return ['invoices'=>[], 'total_count'=>0, 'current_page'=>1, 'per_page'=>$per_page, 'total_pages'=>0];
    }

    $base_select = "
        SELECT
            inv.id AS invoice_id,
            inv.transaction_history_id,
            th.registration_id,
            inv.created_at AS request_date,
            inv.status,
            inv.invoice_file,
            inv.rejected_reason,
            u.email AS user_email,
            p.name AS package_name
    ";
    $base_from = "
        FROM invoice inv
        JOIN transaction_history th ON inv.transaction_history_id = th.id
        JOIN registration r ON th.registration_id = r.id
        JOIN user u ON r.user_id = u.id
        JOIN package p ON r.package_id = p.id
    ";
    $base_where = " WHERE 1 ";

    $where_clauses = [];
    $params = [];
    if (!empty($filters['status']) && in_array($filters['status'], ['pending','approved','rejected'], true)) {
        $db_status = $filters['status'] === 'approved' ? 'approved' : $filters['status'];
        $where_clauses[] = 'inv.status = :status';
        $params[':status'] = $db_status;
    }

    if ($where_clauses) {
        $base_where .= ' AND ' . implode(' AND ', $where_clauses);
    }

    // count query
    $count_sql = "SELECT COUNT(inv.id) " . $base_from . $base_where;
    $stmt = $db->prepare($count_sql);
    $stmt->execute($params);
    $total_count = (int)$stmt->fetchColumn();
    $total_pages = $per_page > 0 ? (int)ceil($total_count / $per_page) : 0;
    $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));

    // data query
    $data_sql = $base_select . $base_from . $base_where . ' ORDER BY inv.created_at DESC LIMIT :limit OFFSET :offset';
    $stmt2 = $db->prepare($data_sql);
    foreach ($params as $key => $val) {
        $stmt2->bindValue($key, $val);
    }
    $stmt2->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt2->bindValue(':offset', ($page -1)*$per_page, PDO::PARAM_INT);
    $stmt2->execute();

    $invoices = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    return [
        'invoices' => $invoices,
        'total_count' => $total_count,
        'current_page' => $page,
        'per_page' => $per_page,
        'total_pages' => $total_pages,
    ];
}
