<?php
// ensure errors are logged to our central log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Thêm dòng này nếu chưa include functions.php để có api_error()
require_once __DIR__ . '/../utils/functions.php';

// Phát hiện API request (JSON) hay Web
function isApiRequest(): bool {
    return (
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false)
    );
}

// Hàm abort dùng chung
function abort(string $message, int $statusCode = 500): void {
    http_response_code($statusCode);
    if (isApiRequest()) {
        // Dùng helper gửi envelope JSON thống nhất
        api_error($message, $statusCode);
    } else {
        // load layout HTML lỗi chung
        require __DIR__ . '/../views/error.php';
    }
    exit;
}

// uncaught exception handler
set_exception_handler(function(Throwable $e) {
    error_log("Uncaught exception: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
    abort('Internal server error.', 500);
});

// fatal error handler
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal error: {$err['message']} in {$err['file']} on line {$err['line']}");
        abort('Internal server error.', 500);
    }
});
