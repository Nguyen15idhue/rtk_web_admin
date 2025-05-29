<?php
// filepath: public/handlers/revenue/get_stats.php
declare(strict_types=1);
header('Content-Type: application/json');

$bootstrap = require_once __DIR__ . '/../../../private/core/page_bootstrap.php';
require_once __DIR__ . '/../../../private/actions/purchase/get_revenue_stats.php';

Auth::ensureAuthorized('revenue_management_view');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $filters = [
        'date_from' => $_GET['date_from'] ?? '',
        'date_to' => $_GET['date_to'] ?? '',
        'status' => $_GET['status'] ?? ''
    ];
    
    $stats = get_revenue_stats($filters);
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error'
    ]);
    error_log("Revenue stats error: " . $e->getMessage());
}
