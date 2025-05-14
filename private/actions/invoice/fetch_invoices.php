<?php
declare(strict_types=1);
require_once __DIR__ . '/../../utils/functions.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once BASE_PATH . '/classes/InvoiceModel.php';   // thêm
Auth::ensureAuthorized('invoice_management');

// Prevent direct access
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    api_forbidden('Forbidden');
}

$bootstrap = require __DIR__ . '/../../core/page_bootstrap.php';
$db        = $bootstrap['db'];
register_shutdown_function(fn() => $db = null);

function fetch_admin_invoices(array $filters = [], int $page = 1, int $per_page = 10): array {
    $model  = new InvoiceModel();                         // thêm
    $offset = ($page - 1) * $per_page;
    $list   = $model->getAll($filters, $per_page, $offset); // thay cho toàn bộ logic SQL thủ công

    // giả sử chỉ trả về dữ liệu, phần total_count/total_pages có thể tính thêm nếu cần
    return [
        'invoices'     => $list,
        'total_count'  => count($list),
        'current_page' => $page,
        'per_page'     => $per_page,
        'total_pages'  => 1
    ];
}
