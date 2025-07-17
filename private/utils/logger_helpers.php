<?php
/**
 * Logger Helper Functions
 * Provides easy access to Logger class methods
 */

// Ensure Logger class is available
if (!class_exists('Logger')) {
    require_once __DIR__ . '/../classes/Logger.php';
}

// Prevent redeclaration if functions already defined elsewhere
if (!function_exists('log_error')) {
    /**
     * Log an error message
     */
    function log_error(string $message, array $context = []): void {
        Logger::error($message, $context);
    }
}

if (!function_exists('log_warning')) {
    /**
     * Log a warning message
     */
    function log_warning(string $message, array $context = []): void {
        Logger::warning($message, $context);
    }
}

if (!function_exists('log_info')) {
    /**
     * Log an info message
     */
    function log_info(string $message, array $context = []): void {
        Logger::info($message, $context);
    }
}

if (!function_exists('log_debug')) {
    /**
     * Log a debug message
     */
    function log_debug(string $message, array $context = []): void {
        Logger::debug($message, $context);
    }
}

if (!function_exists('log_exception')) {
    /**
     * Log an exception
     */
    function log_exception(Throwable $e, array $context = []): void {
        Logger::exception($e, $context);
    }
}
