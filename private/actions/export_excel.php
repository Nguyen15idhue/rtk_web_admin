<?php
// Ensure session is started at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Autoloading and common setup
require_once __DIR__ . '/../includes/page_bootstrap.php'; // This should include functions.php
// Service for Excel generation
require_once __DIR__ . '/../services/ExcelExportService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Export Excel: Invalid request method.');
    api_error('Invalid request method. Only POST is accepted.', 405);
}

$tableName = $_POST['table_name'] ?? null;
$selectedIds = $_POST['ids'] ?? []; // Expecting an array of IDs, can be empty

if (empty($tableName)) {
    error_log('Export Excel: Table name not provided.');
    api_error('Table name is required.', 400);
}

// Sanitize IDs to be integers if they are expected to be numeric
if (!empty($selectedIds)) {
    $selectedIds = array_map('intval', $selectedIds); // Basic sanitization
    $selectedIds = array_filter($selectedIds, function($id) { return $id > 0; }); // Filter out invalid IDs
}

// $db should be available from page_bootstrap.php if it's returned by it.
// If not, uncomment the line below or ensure $db is properly initialized.
// $db = Database::getInstance()->getConnection(); 

$dataToExport = [];
$modelInstance = null;
$modelClass = '';
$fileName = preg_replace('/[^a-z0-9_]+/', '', strtolower($tableName)) . '_export.xlsx'; // Sanitize filename

$modelMapping = [
    'accounts'     => 'AccountModel',
    'guides'       => 'GuideModel',
    'invoices'     => 'InvoiceModel',
    'transactions' => 'TransactionModel',
    'users'        => 'UserModel',
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
        // Ensure $db is passed if the constructor expects it.
        // The $db variable is expected to be globally available or passed from page_bootstrap.php
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
        if (method_exists($modelInstance, 'getAllDataForExport')) {
            $dataToExport = $modelInstance->getAllDataForExport();
        } elseif (method_exists($modelInstance, 'getAll')) {
            $dataToExport = $modelInstance->getAll();
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