<?php

/**
 * Enhanced Logger Class for RTK Web Admin
 * 
 * Features:
 * - Multiple log levels (ERROR, WARNING, INFO, DEBUG)
 * - Separate log files by level and date
 * - Auto log rotation
 * - Stack trace filtering
 * - Compact formatting
 */
class Logger
{
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';

    private static $instance = null;
    private $logDir;
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $maxFiles = 7; // Keep 7 days of logs

    private function __construct()
    {
        $this->logDir = __DIR__ . '/../logs/';
        $this->ensureLogDirectory();
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    /**
     * Log error message
     */
    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->log(self::ERROR, $message, $context);
    }

    /**
     * Log warning message
     */
    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->log(self::WARNING, $message, $context);
    }

    /**
     * Log info message
     */
    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->log(self::INFO, $message, $context);
    }

    /**
     * Log debug message
     */
    public static function debug(string $message, array $context = []): void
    {
        self::getInstance()->log(self::DEBUG, $message, $context);
    }

    /**
     * Log exception with filtered stack trace
     */
    public static function exception(Throwable $e, array $context = []): void
    {
        $message = sprintf(
            'Exception: %s in %s:%d',
            $e->getMessage(),
            basename($e->getFile()),
            $e->getLine()
        );

        $context['stack_trace'] = self::getFilteredStackTrace($e);
        self::getInstance()->log(self::ERROR, $message, $context);
    }

    /**
     * Main logging method
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        
        // Determine caller info
        $caller = $this->getCaller();
        
        // Format the log entry
        $logEntry = $this->formatLogEntry($timestamp, $level, $message, $caller, $context);
        
        // Write to appropriate log file
        $this->writeToFile($level, $date, $logEntry);
        
        // Note: Removed backward compatibility with error.log
        // All errors now go through the new daily log system
    }

    /**
     * Format log entry in compact format
     */
    private function formatLogEntry(string $timestamp, string $level, string $message, array $caller, array $context): string
    {
        $entry = sprintf(
            "[%s] %s: %s",
            $timestamp,
            $level,
            $message
        );

        // Add caller information
        if (!empty($caller['file'])) {
            $entry .= sprintf(" | %s:%d", $caller['file'], $caller['line']);
        }

        // Add context if provided
        if (!empty($context)) {
            $contextStr = $this->formatContext($context);
            if ($contextStr) {
                $entry .= " | Context: {$contextStr}";
            }
        }

        return $entry . PHP_EOL;
    }

    /**
     * Format context data
     */
    private function formatContext(array $context): string
    {
        $formatted = [];
        
        foreach ($context as $key => $value) {
            if ($key === 'stack_trace') {
                $formatted[] = "Stack: " . implode(' → ', array_slice($value, 0, 3));
            } elseif (is_scalar($value)) {
                $formatted[] = "{$key}={$value}";
            } elseif (is_array($value)) {
                $formatted[] = "{$key}=" . json_encode($value);
            }
        }
        
        return implode(', ', $formatted);
    }

    /**
     * Get caller information
     */
    private function getCaller(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        
        // Skip Logger class methods
        foreach ($trace as $frame) {
            if (isset($frame['class']) && $frame['class'] === __CLASS__) {
                continue;
            }
            
            return [
                'file' => isset($frame['file']) ? basename($frame['file']) : 'unknown',
                'line' => $frame['line'] ?? 0
            ];
        }
        
        return ['file' => 'unknown', 'line' => 0];
    }

    /**
     * Get filtered stack trace (remove vendor and internal calls)
     */
    private static function getFilteredStackTrace(Throwable $e): array
    {
        $trace = $e->getTrace();
        $filtered = [];
        
        foreach ($trace as $frame) {
            $file = $frame['file'] ?? '';
            
            // Skip vendor files and internal PHP calls
            if (strpos($file, '/vendor/') !== false || 
                strpos($file, '\\vendor\\') !== false ||
                empty($file)) {
                continue;
            }
            
            $filtered[] = sprintf(
                '%s:%d %s%s%s()',
                basename($file),
                $frame['line'] ?? 0,
                $frame['class'] ?? '',
                $frame['type'] ?? '',
                $frame['function'] ?? ''
            );
            
            // Limit to 5 most relevant frames
            if (count($filtered) >= 5) {
                break;
            }
        }
        
        return $filtered;
    }

    /**
     * Write to level-specific log file
     */
    private function writeToFile(string $level, string $date, string $entry): void
    {
        $filename = sprintf('%s_%s.log', strtolower($level), $date);
        $filepath = $this->logDir . $filename;
        
        // Check file size and rotate if needed
        if (file_exists($filepath) && filesize($filepath) > $this->maxFileSize) {
            $this->rotateLogFile($filepath);
        }
        
        file_put_contents($filepath, $entry, FILE_APPEND | LOCK_EX);
        
        // Clean old log files
        $this->cleanOldLogs($level);
    }

    /**
     * Rotate log file when it gets too large
     */
    private function rotateLogFile(string $filepath): void
    {
        $info = pathinfo($filepath);
        $rotatedFile = sprintf(
            '%s/%s_%s.%s',
            $info['dirname'],
            $info['filename'],
            date('His'),
            $info['extension']
        );
        
        rename($filepath, $rotatedFile);
    }

    /**
     * Clean old log files
     */
    private function cleanOldLogs(string $level): void
    {
        $pattern = $this->logDir . strtolower($level) . '_*.log';
        $files = glob($pattern);
        
        if (count($files) > $this->maxFiles) {
            // Sort by modification time
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove oldest files
            $filesToRemove = array_slice($files, 0, count($files) - $this->maxFiles);
            foreach ($filesToRemove as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    /**
     * Get recent log entries for dashboard
     */
    public static function getRecentLogs(string $level = null, int $limit = 100): array
    {
        $instance = self::getInstance();
        $logs = [];

        // Determine which levels to fetch
        $levelsToFetch = $level ? [strtolower($level)] : ['error', 'warning', 'info', 'debug'];

        foreach ($levelsToFetch as $lvl) {
            // Fetch all log files for this level
            $logFiles = glob($instance->logDir . $lvl . '_*.log');
            rsort($logFiles);
            foreach ($logFiles as $filePath) {
                $content = file_get_contents($filePath);
                $lines = array_filter(explode("\n", $content));
                foreach ($lines as $line) {
                    if (preg_match('/^\[([^\]]+)\] (\w+): (.+)$/', $line, $matches)) {
                        $logs[] = [
                            'timestamp' => $matches[1],
                            'level'     => $matches[2],
                            'message'   => $matches[3],
                            'raw'       => $line
                        ];
                    }
                }
            }
        }

        // Also read from legacy error.log if it exists and we're fetching errors or all
        if ($level === null || $level === 'error') {
            $errorLogPath = $instance->logDir . 'error.log';
            if (file_exists($errorLogPath)) {
                $content = file_get_contents($errorLogPath);
                $lines = array_filter(explode("\n", $content));
                foreach ($lines as $line) {
                    // Try to parse different log formats
                    if (preg_match('/^\[([^\]]+)\] (\w+): (.+)$/', $line, $matches)) {
                        $logs[] = [
                            'timestamp' => $matches[1],
                            'level'     => $matches[2],
                            'message'   => $matches[3],
                            'raw'       => $line
                        ];
                    } elseif (preg_match('/^\[([^\]]+)\] (.+)$/', $line, $matches)) {
                        // Legacy format without level
                        $logs[] = [
                            'timestamp' => $matches[1],
                            'level'     => 'ERROR',
                            'message'   => $matches[2],
                            'raw'       => $line
                        ];
                    }
                }
            }
        }

        // Sort logs by timestamp descending and limit
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($logs, 0, $limit);
    }

    /**
     * Get log statistics
     */
    public static function getLogStats(): array
    {
        $instance = self::getInstance();
        $stats = [
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0
        ];

        foreach (array_keys($stats) as $level) {
            // Pattern for log files of this level
            $pattern = $instance->logDir . $level . '_*.log';
            $files = glob($pattern);

            if (!empty($files)) {
                // Determine today's file path
                $todayFile = $instance->logDir . $level . '_' . date('Y-m-d') . '.log';
                if (file_exists($todayFile)) {
                    $filepath = $todayFile;
                } else {
                    // Fallback to the most recent file by modification time
                    usort($files, function ($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    $filepath = $files[0];
                }

                $stats[$level] = substr_count(file_get_contents($filepath), "\n");
            }
        }

        // Also count from legacy error.log for error stats
        $errorLogPath = $instance->logDir . 'error.log';
        if (file_exists($errorLogPath)) {
            $errorLogContent = file_get_contents($errorLogPath);
            $errorLogLines = substr_count($errorLogContent, "\n");
            $stats['error'] += $errorLogLines;
        }

        return $stats;
    }
}
