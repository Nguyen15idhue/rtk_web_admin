<?php
// Only allow POSTs through this proxy
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Method Not Allowed. Use POST.';
    exit;
}

// Handle export_report_excel action
if (isset($_POST['action']) && $_POST['action'] === 'export_report_excel') {
    // Forward date filters to the report export logic
    $_POST['table_name'] = 'reports';
    $_POST['date_from'] = $_POST['start_date'] ?? null;
    $_POST['date_to'] = $_POST['end_date'] ?? null;
}

// Handle export_revenue_summary action
if (isset($_POST['action']) && $_POST['action'] === 'export_revenue_summary') {
    // Forward to revenue summary export logic
    $_POST['table_name'] = 'revenue_summary';
    // date_from, date_to, and status filters are already in $_POST
}

// Forward to the real exporter in private/actions
require_once __DIR__ . '/../../private/actions/export_excel.php';