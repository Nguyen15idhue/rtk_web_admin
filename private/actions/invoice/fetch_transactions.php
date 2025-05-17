<?php
// filepath: private\actions\invoice\fetch_transactions.php
declare(strict_types=1);

require_once __DIR__ . '/../../utils/functions.php';  

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}

$bootstrap = require_once __DIR__ . '/../../core/page_bootstrap.php';

Auth::ensureAuthorized('invoice_management_view');
if ($bootstrap === true) {
    $bootstrap = $GLOBALS['__PAGE_BOOTSTRAP_INSTANCE_DATA__'];
}
$db        = $bootstrap['db'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});
/**
 * Fetches transactions for the admin invoice management page with filtering and pagination.
 *
 * @param array $filters Associative array of filters:
 *                       'search' => string (searches transaction ID and user email),
 *                       'status' => string ('pending', 'approved', 'rejected', ''), 'approved' maps to 'completed' in DB.
 *                       'date_from' => string (Y-m-d),
 *                       'date_to' => string (Y-m-d),
 *                       'package_id' => int (ID of the package),
 *                       'province' => string (Province name),
 *                       'type' => string ('purchase', 'renewal')
 * @param int $page Current page number (1-based).
 * @param int $per_page Number of items per page.
 * @return array An array containing 'transactions', 'total_count', 'current_page', 'per_page', 'total_pages'. Returns empty array on error.
 */
function fetch_admin_transactions(array $filters = [], int $page = 1, int $per_page = 10): array {
    global $db;
    try {
        if (!$db) {
            throw new Exception("Failed to connect to the database.");
        }

        // --- Cập nhật SELECT từ transaction_history ---
        $base_select = "
            SELECT
                th.id AS registration_id,                   /* dùng chung key để frontend không thay đổi */
                th.transaction_type,                        /* purchase | renewal */
                th.amount AS amount,                        /* amount for UI */
                CASE 
                    WHEN th.status = 'completed' THEN 'active'
                    WHEN th.status = 'failed'    THEN 'rejected'
                    ELSE th.status
                END AS registration_status,               /* map to UI statuses */
                th.created_at AS request_date,
                r.rejection_reason,
                u.email AS user_email,
                p.name AS package_name,
                th.payment_image AS payment_image,         /* use transaction_history.payment_image */
                l.province,
                th.voucher_id,
                v.code AS voucher_code,
                v.discount_value,
                v.voucher_type,
                v.description AS voucher_description,
                v.start_date AS voucher_start_date,
                v.end_date AS voucher_end_date
        ";
        $base_from  = "
            FROM transaction_history th
            JOIN registration r    ON th.registration_id = r.id AND r.deleted_at IS NULL
            JOIN user u            ON r.user_id = u.id
            JOIN package p         ON r.package_id = p.id
            LEFT JOIN location l   ON l.id = r.location_id
            LEFT JOIN voucher v    ON th.voucher_id = v.id
        ";
        $base_where = " WHERE 1=1 "; // always true, filters tiếp theo dùng AND

        // Đếm và data query
        $count_query = "SELECT COUNT(th.id) " . $base_from . $base_where;
        $data_query  = $base_select . $base_from . $base_where;

        $where_clauses = [];
        $params = [];

        // filter search
        if (!empty($filters['search'])) {
            $s = '%'.trim($filters['search']).'%';
            $where_clauses[] = "(CAST(th.id AS CHAR) LIKE :search_id OR u.email LIKE :search_email)";
            $params[':search_id']    = $s;
            $params[':search_email'] = $s;
        }
        // filter status (pending/approved/rejected)
        if (!empty($filters['status'])) {
            // properly parenthesize nested ternary
            $map = $filters['status'] === 'approved'
                ? 'completed'
                : ( $filters['status'] === 'rejected' ? 'failed' : $filters['status'] );
            $where_clauses[] = "th.status = :status";
            $params[':status'] = $map;
        }
        // filter date_from/to
        if (!empty($filters['date_from'])) {
            $where_clauses[] = "DATE(th.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where_clauses[] = "DATE(th.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        // filter package
        if (!empty($filters['package_id'])) {
            $where_clauses[] = "r.package_id = :package_id";
            $params[':package_id'] = (int)$filters['package_id'];
        }
        // filter province
        if (!empty($filters['province'])) {
            $where_clauses[] = "l.province = :province";
            $params[':province'] = $filters['province'];
        }
        // NEW: filter transaction type
        if (!empty($filters['type'])) {
            $where_clauses[] = "th.transaction_type = :tx_type";
            $params[':tx_type'] = $filters['type'];
        }

        if (!empty($where_clauses)) {
            $sql = " AND " . implode(" AND ", $where_clauses);
            $count_query .= $sql;
            $data_query  .= $sql;
        }

        // Get total count for pagination
        $stmt_count = $db->prepare($count_query);
        $stmt_count->execute($params); // Execute count query with filter params
        $total_count = (int) $stmt_count->fetchColumn();
        $total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 0;
        // Ensure page number is valid
        $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));

        // Add ordering and pagination to the data query
        $data_query .= " ORDER BY th.created_at DESC"; // Order by request date, newest first
        $offset = ($page - 1) * $per_page;
        // IMPORTANT: Use named parameters for LIMIT and OFFSET that don't conflict with filter params
        $data_query .= " LIMIT :limit_val OFFSET :offset_val";

        // Prepare the data query
        $stmt_data = $db->prepare($data_query);

        // Bind filter parameters explicitly
        foreach ($params as $key => $val) {
            $stmt_data->bindValue($key, $val); // Use bindValue for simplicity here
        }

        // Bind pagination parameters explicitly as integers
        $limit_val = (int)$per_page;
        $offset_val = (int)$offset;
        $stmt_data->bindValue(':limit_val', $limit_val, PDO::PARAM_INT);
        $stmt_data->bindValue(':offset_val', $offset_val, PDO::PARAM_INT);

        // Execute the data query (no arguments needed as all params are bound)
        $stmt_data->execute();

        $transactions = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        return [
            'transactions' => $transactions,
            'total_count' => $total_count,
            'current_page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
        ];

    } catch (PDOException $e) {
        error_log("Database error in fetch_admin_transactions: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
        error_log("Trace: " . $e->getTraceAsString());      // <-- Added detailed stack trace
        return [
            'transactions' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    } catch (Exception $e) {
        error_log("General error in fetch_admin_transactions: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());      // <-- Added detailed stack trace
        return [
            'transactions' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    }
}
?>