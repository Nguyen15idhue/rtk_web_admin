<?php
// filepath: private/actions/voucher/fetch_vouchers.php
declare(strict_types=1);

require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('voucher_management_view');
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden: Direct access not allowed');
}
require_once __DIR__ . '/../../classes/VoucherModel.php';
/**
 * Fetches vouchers with pagination and optional filtering.
 */
function fetch_paginated_vouchers(array $filters = [], int $page = 1, int $per_page = 10): array {
    try {
        $voucherModel = new VoucherModel();
        return $voucherModel->fetchPaginated($filters, $page, $per_page);
    } catch (PDOException $e) {
        error_log("Database error in fetch_paginated_vouchers: " . $e->getMessage());
        return ['vouchers'=>[], 'total_count'=>0, 'current_page'=>1, 'per_page'=>$per_page, 'total_pages'=>0];
    }
}
