<?php
/**
 * Logger Helper Functions
 * Provides easy access to Logger class methods
 */

// Ensure Logger class is available
if (!class_exists('Logger')) {
    require_once __DIR__ . '/../classes/Logger.php';
}

/**
 * Log an error message
 */
function log_error(string $message, array $context = []): void {
    Logger::error($message, $context);
}

/**
 * Log a warning message
 */
function log_warning(string $message, array $context = []): void {
    Logger::warning($message, $context);
}

/**
 * Log an info message
 */
function log_info(string $message, array $context = []): void {
    Logger::info($message, $context);
}

/**
 * Log a debug message
 */
function log_debug(string $message, array $context = []): void {
    Logger::debug($message, $context);
}

/**
 * Log an exception
 */
function log_exception(Throwable $e, array $context = []): void {
    Logger::exception($e, $context);
}
