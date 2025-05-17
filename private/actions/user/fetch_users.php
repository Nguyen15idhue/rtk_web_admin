<?php
// filepath: private\actions\user\fetch_users.php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';  // add helper
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('user_management_view'); 
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
// --- END Role check ---

require_once __DIR__ . '/../../classes/UserModel.php'; // Include UserModel

/**
 * Fetches users with pagination and optional filtering using UserModel.
 *
 * @param array $filters Associative array of filters (e.g., ['search' => 'term', 'status' => 'active']).
 * @param int $page Current page number (1-based).
 * @param int $per_page Number of items per page.
 * @return array An array containing 'users', 'total_count', 'current_page', 'per_page', 'total_pages'. Returns empty array on error.
 */
function fetch_paginated_users(array $filters = [], int $page = 1, int $per_page = 10): array {
    try {
        $userModel = new UserModel();
        return $userModel->fetchPaginated($filters, $page, $per_page);

    } catch (PDOException $e) {
        error_log("Database error in fetch_paginated_users (via UserModel): " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return [
            'users' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    } catch (Exception $e) {
        error_log("General error in fetch_paginated_users (via UserModel): " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return [
            'users' => [], 'total_count' => 0, 'current_page' => 1,
            'per_page' => $per_page, 'total_pages' => 0
        ];
    }
    // The finally block for closing DB connection is removed as UserModel's destructor handles it.
}
?>
