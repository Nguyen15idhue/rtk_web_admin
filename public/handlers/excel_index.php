<?php
// Only allow POSTs through this proxy
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Method Not Allowed. Use POST.';
    exit;
}
// Forward to the real exporter in private/actions
require_once __DIR__ . '/../../private/actions/export_excel.php';