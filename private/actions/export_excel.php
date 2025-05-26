<?php
// Autoloading and common setup
require_once __DIR__ . '/../core/page_bootstrap.php'; // This should include functions.php
// Service for Excel generation
require_once __DIR__ . '/../services/ExcelExportService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Export Excel: Invalid request method.');
    api_error('Invalid request method. Only POST is accepted.', 405);
    exit; // It's good practice to exit after sending an error response if api_error doesn't exit itself.
}

$tableName = $_POST['table_name'] ?? null;

// Determine which set of IDs to use based on the button pressed
$ids_for_export = [];
if (isset($_POST['export_selected_excel'])) { // Check if 'Export Selected' button was clicked
    $raw_selected_ids = $_POST['selected_ids'] ?? null; // Use IDs from checkboxes
    if (is_string($raw_selected_ids) && !empty($raw_selected_ids)) {
        $ids_for_export = explode(',', $raw_selected_ids);
    } elseif (is_array($raw_selected_ids)) {
        $ids_for_export = $raw_selected_ids;
    } else {
        $ids_for_export = []; // Default to empty array if not a non-empty string or array
    }
} else {
    $ids_for_export = $_POST['ids'] ?? []; // Fallback to existing 'ids' parameter for backward compatibility or other forms
}

if (empty($tableName)) {
    error_log('Export Excel: Table name not provided.');
    api_error('Table name is required.', 400);
    exit; // Also here.
}

// START: Special handling for 'reports' table export
if ($tableName === 'reports') {
    // Read date filters from POST, defaulting to current month if not provided
    $start_date     = $_POST['date_from'] ?? date('Y-m-01');
    $end_date       = $_POST['date_to']   ?? date('Y-m-t');
    $start_datetime = $start_date . ' 00:00:00';
    $end_datetime   = $end_date   . ' 23:59:59';

    // Ensure $db (from page_bootstrap.php) is available and passed as $pdo
    // The $db variable should be globally available after page_bootstrap.php is included.
    if (!isset($db) || !$db instanceof PDO) {
        error_log("Export Excel (Reports): Database connection (\$db) not available or invalid.");
        api_error("Internal server error: Database connection not available for reports.", 500);
        exit; // And here.
    }
    $pdo = $db; // Make $pdo available for process_reports_data.php

    // Load and process report data
    // $start_datetime and $end_datetime will be used by process_reports_data.php
    require_once __DIR__ . '/report/process_reports_data.php';

    // Define fields expected from process_reports_data.php
    $fields = [
        'total_registrations', 'new_registrations', 'active_accounts', 'locked_accounts',
        'active_survey_accounts', 'new_active_survey_accounts', 'expiring_accounts', 'expired_accounts',
        'total_sales', 'completed_transactions', 'pending_transactions', 'failed_transactions',
        'new_referrals', 'commission_generated', 'commission_paid', 'commission_pending'
    ];

    $report_row_data = [];
    foreach ($fields as $field) {
        if (isset($$field)) {
            $report_row_data[$field] = $$field;
        } else {
            // Log if a field is unexpectedly missing and provide a default value
            error_log("Export Excel (Reports): Report field '{$field}' was not set after process_reports_data.php. Defaulting to 0 or empty.");
            $report_row_data[$field] = 0; // Or use '' or 'N/A' depending on expected data type
        }
    }
    $dataToExport = [$report_row_data]; // Prepare data for Excel export

    // Export directly and terminate script
    ExcelExportService::export($dataToExport, 'reports_' . date('Ymd_His') . '.xlsx');
    exit; // IMPORTANT: Stop script execution after handling report export
}
// END: Special handling for 'reports' table export

// Sanitize IDs to be integers if they are expected to be numeric
if (!empty($ids_for_export)) {
    $selectedIds = array_map('intval', $ids_for_export); // Basic sanitization for the determined ID list
    $selectedIds = array_filter($selectedIds, function($id) { return $id > 0; }); // Filter out invalid IDs
} else {
    $selectedIds = []; // Ensure $selectedIds is an empty array if no IDs are provided
}

$dataToExport = [];
$modelInstance = null;
$modelClass = '';
$fileName = preg_replace('/[^a-z0-9_]+/', '', strtolower($tableName)) . '_export.xlsx'; // Sanitize filename

$modelMapping = [
    'accounts'         => 'AccountModel',
    'guides'           => 'GuideModel',
    'invoices'         => 'InvoiceModel',
    'transactions'     => 'TransactionModel',
    'users'            => 'UserModel',
    'stations'         => 'StationModel',
    'vouchers'         => 'VoucherModel', // support exporting vouchers
    'support_requests' => 'SupportRequestModel',
    'commissions'      => 'CommissionModel',      // Added for commissions export
    'withdrawal_requests' => 'WithdrawalRequestModel', // Added for withdrawal requests export
];

if (isset($modelMapping[$tableName])) {
    $modelClass = $modelMapping[$tableName];
    $modelPath = __DIR__ . '/../classes/' . $modelClass . '.php';

    if (file_exists($modelPath)) {
        require_once $modelPath;
    } else {
        error_log("Export Excel: Model file '{$modelPath}' not found for table '{$tableName}'.");
        api_error("Configuration error: Model file for '{$modelClass}' not found.", 500);
    }

    if (class_exists($modelClass)) {
        if (!isset($db) || !$db instanceof PDO) {
            error_log("Export Excel: Database connection (\$db) not available or invalid.");
            api_error("Internal server error: Database connection not available.", 500);
        }
        $modelInstance = new $modelClass($db);
    } else {
        error_log("Export Excel: Model class '{$modelClass}' not found for table '{$tableName}' after attempting to require '{$modelPath}'.");
        api_error("Configuration error: Model class '{$modelClass}' not found.", 500);
    }
} else {
    error_log("Export Excel: Unknown or unmapped table name '{$tableName}'.");
    api_error("Invalid table name '{$tableName}' specified for export.", 400);
}

try {
    if (!empty($selectedIds)) {
        if (method_exists($modelInstance, 'getDataByIdsForExport')) {
            $dataToExport = $modelInstance->getDataByIdsForExport($selectedIds);
        } elseif (method_exists($modelInstance, 'getByIds')) {
            $dataToExport = $modelInstance->getByIds($selectedIds);
        } else {
            error_log("Export Excel: Method like getDataByIdsForExport or getByIds not found in {$modelClass} for selected IDs.");
            $dataToExport = []; // Proceed with empty data to generate an empty Excel with a message
        }
    } else {
        // If not exporting by specific IDs, collect filter parameters from POST for general export
        $filters = [];
        if (isset($_POST['search']) && trim($_POST['search']) !== '') {
            $filters['search'] = trim($_POST['search']);
        }
        if (isset($_POST['status']) && trim($_POST['status']) !== '') {
            $filters['status'] = trim($_POST['status']);
        }
        if (isset($_POST['date_from']) && trim($_POST['date_from']) !== '') {
            $filters['date_from'] = trim($_POST['date_from']);
        }
        if (isset($_POST['date_to']) && trim($_POST['date_to']) !== '') {
            $filters['date_to'] = trim($_POST['date_to']);
        }
        // Add any other general filters that might be passed via POST

        if (method_exists($modelInstance, 'getAllDataForExport')) {
            $dataToExport = $modelInstance->getAllDataForExport($filters); // Pass collected filters
        } elseif (method_exists($modelInstance, 'getAll')) {
            // Fallback if getAllDataForExport doesn't exist, try passing filters to getAll
            // The model's getAll method would need to be designed to handle an optional filters array
            $dataToExport = $modelInstance->getAll($filters); 
        } else {
            error_log("Export Excel: Method like getAllDataForExport or getAll not found in {$modelClass} for all data.");
            $dataToExport = []; // Proceed with empty data
        }
    }
} catch (Exception $e) {
    error_log("Export Excel: Error fetching data from model {$modelClass}: " . $e->getMessage());
    api_error("Error fetching data: " . $e->getMessage(), 500);
}

if (empty($dataToExport)) {
    $messageRowValue = !empty($selectedIds) ? 'No data found for the selected items.' : 'No data found for this table.';
    if (isset($_SESSION['last_headers']) && is_array($_SESSION['last_headers']) && !empty($_SESSION['last_headers'])) {
        $dataToExport = [array_fill_keys($_SESSION['last_headers'], $messageRowValue)];
    } else {
        $dataToExport = [['message' => $messageRowValue]];
    }
}

if (!empty($dataToExport) && is_array($dataToExport)) {
    $firstRow = reset($dataToExport);
    if (is_object($firstRow)) {
        $dataToExport = array_map(function($object) {
            return (array) $object;
        }, $dataToExport);
    }
}

try {
    ExcelExportService::export($dataToExport, $fileName);
    // ExcelExportService calls exit, so no code below this will run on success.
} catch (Exception $e) {
    error_log("Excel Export Service Error: " . $e->getMessage());
    // If ExcelExportService fails, it might have already sent some headers.
    // api_error will attempt to set JSON headers, which might be an issue if other headers are sent.
    // send_json_response inside api_error checks for headers_sent().
    api_error('An error occurred while generating the Excel file. Details: ' . $e->getMessage(), 500);
}
// Safeguard exit, though ExcelExportService::export() or api_error() should handle it.
exit;
?>