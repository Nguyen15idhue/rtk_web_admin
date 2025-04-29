<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\invoice\fetch_transactions.php
declare(strict_types=1);

// Ensure this script is not accessed directly if it's meant to be included
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
     http_response_code(403);
     die("Forbidden: Direct access is not allowed.");
}
// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../utils/functions.php'; // Include helpers

/**
 * Fetches transactions for the admin invoice management page with filtering and pagination.
 *
 * @param array $filters Associative array of filters:
 *                       'search' => string (searches registration ID and user email),
 *                       'status' => string ('pending', 'approved', 'rejected', ''), 'approved' maps to 'active' in DB.
 *                       'date_from' => string (Y-m-d),
 *                       'date_to' => string (Y-m-d),
 *                       'package_id' => int (ID of the package),
 *                       'province' => string (Province name)
 * @param int $page Current page number (1-based).
 * @param int $per_page Number of items per page.
 * @return array An array containing 'transactions', 'total_count', 'current_page', 'per_page', 'total_pages'. Returns empty array on error.
 */
function fetch_admin_transactions(array $filters = [], int $page = 1, int $per_page = 10): array {
    try {
        // Instantiate Database and get connection
        $db = Database::getInstance()->getConnection();
        if (!$db) {
            throw new Exception("Failed to connect to the database.");
        }

        // Base query joining necessary tables
        $base_select = "
            SELECT
                r.id as registration_id,
                r.created_at as request_date,
                r.total_price as amount,
                r.status as registration_status,
                r.rejection_reason, -- Fetch rejection reason
                u.email as user_email,
                p.name as package_name,
                pay.payment_image, -- Get proof image filename from payment table
                r.location_id,
                l.province AS province
        ";
        $base_from = "
            FROM registration r
            JOIN user u ON r.user_id = u.id
            JOIN package p ON r.package_id = p.id
            LEFT JOIN payment pay ON r.id = pay.registration_id -- Use LEFT JOIN in case payment proof hasn't been uploaded yet
            LEFT JOIN location l ON l.id = r.location_id
        ";
        $base_where = " WHERE r.deleted_at IS NULL "; // Exclude soft-deleted registrations

        $count_query = "SELECT COUNT(r.id) " . $base_from . $base_where;
        $data_query = $base_select . $base_from . $base_where;

        $where_clauses = [];
        $params = []; // Parameters for filtering (used in both count and data queries)

        // Apply filters
        if (!empty($filters['search'])) {
            $search_term = '%' . trim($filters['search']) . '%';
            // Use distinct placeholders for each part of the OR condition
            $where_clauses[] = "(CAST(r.id AS CHAR) LIKE :search_id OR u.email LIKE :search_email)";
            // Add both parameters to the array, even if they use the same value
            $params[':search_id'] = $search_term;
            $params[':search_email'] = $search_term;
        }
        if (!empty($filters['status'])) {
            // Map UI 'approved' back to DB 'active'
            $db_status = ($filters['status'] === 'approved') ? 'active' : $filters['status'];
            if (in_array($db_status, ['pending', 'active', 'rejected'])) {
                $where_clauses[] = "r.status = :status";
                $params[':status'] = $db_status;
            }
        }
        if (!empty($filters['date_from'])) {
            // Validate date format if needed
            $where_clauses[] = "DATE(r.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            // Validate date format if needed
            $where_clauses[] = "DATE(r.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // --- MỞ RỘNG FILTERS: package_id và province ---
        if (!empty($filters['package_id'])) {
            $where_clauses[] = "r.package_id = :package_id";
            $params[':package_id'] = (int)$filters['package_id'];
        }
        if (!empty($filters['province'])) {
            $where_clauses[] = "l.province = :province";
            $params[':province'] = $filters['province'];
        }

        // Append filter conditions to queries
        if (!empty($where_clauses)) {
            $where_sql = " AND " . implode(" AND ", $where_clauses);
            $count_query .= $where_sql;
            $data_query .= $where_sql;
        }

        // --- LOGGING START ---
        error_log("Admin Transactions - Count Query: " . $count_query);
        error_log("Admin Transactions - Count Params: " . print_r($params, true));
        // --- LOGGING END ---

        // Get total count for pagination
        $stmt_count = $db->prepare($count_query);
        $stmt_count->execute($params); // Execute count query with filter params
        $total_count = (int) $stmt_count->fetchColumn();
        $total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 0;
        // Ensure page number is valid
        $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));


        // Add ordering and pagination to the data query
        $data_query .= " ORDER BY r.created_at DESC"; // Order by request date, newest first
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

        // --- LOGGING START ---
        error_log("Admin Transactions - Data Query (Recheck): " . $data_query);
        error_log("Admin Transactions - All Params (bound via bindValue): " . print_r(array_merge($params, [':limit_val' => $limit_val, ':offset_val' => $offset_val]), true));
        // --- LOGGING END ---

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
        // Log the error including the specific PDO error code if available
        error_log("Database error in fetch_admin_transactions: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
        // Return an empty structure or throw exception depending on desired error handling
        return [
            'transactions' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    } catch (Exception $e) {
        error_log("General error in fetch_admin_transactions: " . $e->getMessage());
        return [
            'transactions' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    }
}
?>