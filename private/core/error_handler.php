<?php
// ensure errors are logged to our central log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

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
        require __DIR__ . '/../../public/pages/error.php';
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
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR])) { // Added E_USER_ERROR and E_RECOVERABLE_ERROR for broader capture
        
        $baseErrorMessage = 'Internal server error.';
        $detailsForLog = "Fatal error: {$err['message']} in {$err['file']} on line {$err['line']}";

        if (isApiRequest()) {
            $capturedOutput = '';
            if (ob_get_level() > 0) {
                $capturedOutput = ob_get_contents(); // Capture before any potential cleaning by abort()
            }

            $outputLogged = false;
            $isLikelyOurJsonError = false;

            if (!empty($capturedOutput)) {
                // Check if the captured output is likely a JSON error response we might generate
                $trimmedOutput = trim($capturedOutput);
                $isLikelyOurJsonError = (strpos($trimmedOutput, '{"success":false') === 0 || strpos($trimmedOutput, '{"status":"error"') === 0);

                if (!$isLikelyOurJsonError) {
                    $logFilePath = __DIR__ . '/../logs/error.log'; // Already configured by ini_set, but good for clarity
                    $logFileDir = dirname($logFilePath);

                    // Ensure logs directory exists (it should, due to ini_set, but good practice)
                    if (!is_dir($logFileDir)) {
                        @mkdir($logFileDir, 0775, true);
                    }

                    $canWriteLog = is_writable($logFileDir) || (file_exists($logFilePath) && is_writable($logFilePath));

                    if ($canWriteLog) {
                        $logMessage = sprintf(
                            "[%s] Captured unexpected output during API shutdown:\\nError Type: %s, Message: %s in %s on line %s\\nCaptured Output:\\n%s\\n---\\n",
                            date('Y-m-d H:i:s'),
                            $err['type'],
                            $err['message'],
                            $err['file'],
                            $err['line'],
                            $capturedOutput
                        );
                        // Append to the existing error.log, which also gets PHP's own error logging
                        file_put_contents($logFilePath, $logMessage, FILE_APPEND);
                        $outputLogged = true;
                    } else {
                        // Fallback: use PHP's error_log if specific file isn't writable
                        error_log("error_handler.php: Failed to write to $logFilePath. Captured unexpected output during API shutdown. Error: {$err['message']}. Output (first 1000 chars): " . substr($capturedOutput, 0, 1000));
                    }
                }
            }

            if ($outputLogged) {
                $baseErrorMessage .= ' (Details of unexpected output were logged.)';
                $detailsForLog .= ". Unexpected output was logged.";
            } elseif (!empty($capturedOutput) && !$isLikelyOurJsonError) {
                $baseErrorMessage .= ' (Unexpected output was present; logging attempted/skipped. Check server logs.)';
                $detailsForLog .= ". Unexpected output was present but logging might have failed or was skipped.";
            }
            
            error_log($detailsForLog); // Log the core error detail regardless
            
            // abort() will handle sending the JSON response and exiting.
            // It's crucial that ob_get_contents() was called *before* abort() might clean buffers.
            // abort() itself calls api_error() which calls send_json_response() that has ob_clean() if headers not sent.
            // We need to ensure our capturedOutput is taken before that.
            // The current structure of abort -> api_error -> send_json_response (which might clean buffers)
            // means we must capture $capturedOutput *before* calling abort().
            
            // If headers are already sent (e.g., by the unexpected HTML output),
            // abort() -> api_error() -> send_json_response() will log a warning
            // but won't be able to send a new JSON response. The logging of $capturedOutput is key here.
            if (headers_sent() && !$isLikelyOurJsonError && !empty($capturedOutput)) {
                 error_log("error_handler.php: Headers already sent with unexpected content. The content was logged: " . ($outputLogged ? "Yes" : "Attempted/No"));
                 // We can't call abort() to send a JSON response. The script will terminate after this shutdown function.
                 // The HTML error is already on its way to the client. The log is our record.
            } else if (!headers_sent()) {
                // Clean any buffers *now* before abort sends its JSON
                // This is to prevent our capturedOutput from being sent *again* if it wasn't cleaned by PHP itself.
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                abort($baseErrorMessage, 500);
            } else {
                // Headers sent, but it might have been our own JSON error from a previous handler, or empty output.
                // The error_log($detailsForLog) above is the main record.
                // No further action needed here as abort() would not be able to send JSON.
            }

        } else { // Not an API request
            error_log($detailsForLog);
            // For non-API requests, abort() will show an HTML error page.
            // If headers are already sent with HTML, the error page might not display correctly,
            // but the error is logged.
            if (!headers_sent()) {
                 while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }
            abort($baseErrorMessage, 500); // This will show an HTML error page
        }
    }
});
