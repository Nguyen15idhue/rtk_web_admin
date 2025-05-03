<?php
// ensure errors are logged to our central log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// uncaught exception handler
set_exception_handler(function(Throwable $e) {
    error_log("Uncaught exception: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit;
});

// fatal error handler
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal error: {$err['message']} in {$err['file']} on line {$err['line']}");
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error.']);
        exit;
    }
});
