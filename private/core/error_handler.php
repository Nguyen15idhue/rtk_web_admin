<?php
// Enable error logging but let our Logger class handle the file paths
ini_set('log_errors', 1);
ini_set('error_log', ''); // Disable default error log

require_once __DIR__ . '/../utils/functions.php';
require_once __DIR__ . '/../classes/Logger.php';

// Custom error handler to route PHP errors through our Logger
set_error_handler(function($severity, $message, $file, $line) {
    // Don't handle error if error reporting is disabled
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $context = [
        'file' => basename($file),
        'line' => $line,
        'severity' => $severity
    ];
    
    // Map PHP error levels to our log levels
    switch ($severity) {
        case E_ERROR:
        case E_USER_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_RECOVERABLE_ERROR:
            Logger::error("PHP Error: $message", $context);
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
            Logger::warning("PHP Warning: $message", $context);
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            Logger::info("PHP Notice: $message", $context);
            break;
        default:
            Logger::debug("PHP $message", $context);
            break;
    }
    
    // Don't execute the default PHP error handler
    return true;
});

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
        require __DIR__ . '/../../public/pages/error.php';
    }
    exit;
}

// uncaught exception handler
set_exception_handler(function(Throwable $e) {
    Logger::exception($e, [
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
    abort('Internal server error.', 500);
});

// fatal error handler
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR])) {
        
        $baseErrorMessage = 'Internal server error.';
        $context = [
            'error_type' => $err['type'],
            'file' => basename($err['file']),
            'line' => $err['line'],
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ];

        Logger::error("Fatal error: {$err['message']}", $context);

        if (isApiRequest()) {
            $capturedOutput = '';
            if (ob_get_level() > 0) {
                $capturedOutput = ob_get_contents();
            }

            $isLikelyOurJsonError = false;
            if (!empty($capturedOutput)) {
                $trimmedOutput = trim($capturedOutput);
                $isLikelyOurJsonError = (strpos($trimmedOutput, '{"success":false') === 0 || strpos($trimmedOutput, '{"status":"error"') === 0);

                if (!$isLikelyOurJsonError) {
                    Logger::warning("Unexpected output during API shutdown", [
                        'output_length' => strlen($capturedOutput),
                        'output_preview' => substr($capturedOutput, 0, 200)
                    ]);
                }
            }
            
            if (headers_sent() && !$isLikelyOurJsonError && !empty($capturedOutput)) {
                Logger::error("Headers already sent with unexpected content");
            } else if (!headers_sent()) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                abort($baseErrorMessage, 500);
            }

        } else { // Not an API request
            if (!headers_sent()) {
                 while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }
            abort($baseErrorMessage, 500);
        }
    }
});
