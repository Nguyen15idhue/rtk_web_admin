<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\user\fetch_users.php
declare(strict_types=1);

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
     http_response_code(403);
     die("Forbidden: Direct access is not allowed.");
}
// --- END Role check ---

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';

/**
 * Fetches users with pagination and optional filtering.
 *
 * @param array $filters Associative array of filters (e.g., ['search' => 'term', 'status' => 'active']).
 * @param int $page Current page number (1-based).
 * @param int $per_page Number of items per page.
 * @return array An array containing 'users', 'total_count', 'current_page', 'per_page', 'total_pages'. Returns empty array on error.
 */
function fetch_paginated_users(array $filters = [], int $page = 1, int $per_page = 10): array {
    try {
        $db = (new Database())->getConnection();
        if (!$db) {
            throw new Exception("Failed to connect to the database.");
        }

        $base_select = "SELECT id, username, email, phone, is_company, company_name, tax_code, created_at, updated_at, deleted_at";
        $base_from = " FROM user";
        $base_where = " WHERE 1=1 "; // Start with a true condition
        $params = [];

        // --- Apply Filters --- 
        if (!empty($filters['search'])) {
            $search_term = '%' . trim($filters['search']) . '%';
            $base_where .= " AND (email LIKE :search OR username LIKE :search OR company_name LIKE :search)";
            $params[':search'] = $search_term;
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $base_where .= " AND deleted_at IS NULL";
            } elseif ($filters['status'] === 'inactive') {
                $base_where .= " AND deleted_at IS NOT NULL";
            }
        }
        // Add more filters here if needed

        // --- Count Total Items --- 
        $count_query = "SELECT COUNT(*) " . $base_from . $base_where;
        $stmt_count = $db->prepare($count_query);
        $stmt_count->execute($params);
        $total_count = (int) $stmt_count->fetchColumn();

        // --- Calculate Pagination --- 
        $total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 0;
        $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($page - 1) * $per_page;

        // --- Fetch Data for Current Page --- 
        $data_query = $base_select . $base_from . $base_where;
        $data_query .= " ORDER BY created_at DESC"; // Default order
        $data_query .= " LIMIT :limit OFFSET :offset";

        $stmt_data = $db->prepare($data_query);

        // Bind filter parameters
        foreach ($params as $key => &$val) {
            $stmt_data->bindParam($key, $val);
        }
        unset($val); // break reference

        // Bind pagination parameters
        $stmt_data->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt_data->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt_data->execute();
        $users = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        return [
            'users' => $users,
            'total_count' => $total_count,
            'current_page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
        ];

    } catch (PDOException $e) {
        error_log("Database error in fetch_paginated_users: " . $e->getMessage());
        return [
            'users' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    } catch (Exception $e) {
        error_log("General error in fetch_paginated_users: " . $e->getMessage());
        return [
            'users' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    }
}
?>
