<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\handle_account_list.php

// This script assumes $db is available from the including context (e.g., page_bootstrap.php)
if (!isset($db)) {
    die("Database connection not available in handle_account_list.php");
}

require_once __DIR__ . '/../../classes/AccountModel.php'; // Include the AccountModel

// --- Filtering ---
$filters = [
    'search' => filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
    'package' => filter_input(INPUT_GET, 'package', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
    'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?: '',
];

// --- Pagination ---
$items_per_page = 10; // Consider making this configurable
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

// --- Data Fetching ---
$accountModel = new AccountModel($db);

$total_items = $accountModel->getTotalAccountsCount($filters);
$total_pages = ($items_per_page > 0) ? ceil($total_items / $items_per_page) : 0;

// Adjust current page if it's out of bounds
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
} elseif ($current_page < 1) {
    $current_page = 1;
}

$offset = ($current_page - 1) * $items_per_page;
$accounts = $accountModel->getAccounts($filters, $items_per_page, $offset);
// Note: If search still doesn't work for accounts,
// verify the implementation of the getAccounts method within the AccountModel class.
// Ensure the WHERE clause correctly uses the 'search' filter passed in $filters.

// --- Build Pagination URL ---
// Remove 'page' param from existing query string to build the base URL
$query_params = $_GET;
unset($query_params['page']);
$pagination_query = http_build_query(array_filter($query_params)); // Use array_filter to remove empty params if desired
$pagination_base_url = '?' . $pagination_query . (empty($pagination_query) ? '' : '&');


// Return the processed data
return [
    'filters' => $filters,
    'accounts' => $accounts,
    'total_items' => $total_items,
    'total_pages' => $total_pages,
    'current_page' => $current_page,
    'items_per_page' => $items_per_page,
    'pagination_base_url' => $pagination_base_url,
];
?>