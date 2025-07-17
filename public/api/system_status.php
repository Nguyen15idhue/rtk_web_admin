<?php
/**
 * System Status API Endpoint
 * Returns current system health status for real-time monitoring
 */

// Global error handlers in private/core/error_handler.php will manage fatal errors and uncaught exceptions.
// Local error handling here is primarily for specific check failures to structure the JSON response.

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Include required files
require_once '../../private/config/database.php';
require_once '../../private/classes/Database.php'; // Assuming Database class is correctly autoloaded or included
require_once '../../private/utils/logger_helpers.php';

try {
    // Initialize response
    $response = [
        'status' => 'online',
        'message' => 'Hệ thống đang hoạt động bình thường',
        'timestamp' => date('Y-m-d H:i:s'),
        'checks' => []
    ];

    // Check database connection
    try {
        $db = Database::getInstance(); // Use Singleton pattern
        $connection = $db->getConnection();
        
        if ($connection) {
            // Test database with a simple query
            $stmt = $connection->prepare("SELECT 1 as test");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result && $result['test'] == 1) {
                $response['checks']['database'] = [
                    'status' => 'ok',
                    'message' => 'Kết nối cơ sở dữ liệu bình thường'
                ];
            } else {
                throw new Exception('Database query failed');
            }
        } else {
            throw new Exception('Cannot establish database connection');
        }
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Lỗi kết nối cơ sở dữ liệu';
        $response['checks']['database'] = [
            'status' => 'error',
            'message' => 'Không thể kết nối cơ sở dữ liệu: ' . $e->getMessage()
        ];
    }

    // Check disk space (if system has adequate free space)
    try {
        $freeBytes = disk_free_space(".");
        $totalBytes = disk_total_space(".");
        
        if ($freeBytes !== false && $totalBytes !== false) {
            $freePercentage = ($freeBytes / $totalBytes) * 100;
            
            if ($freePercentage > 10) {
                $response['checks']['disk_space'] = [
                    'status' => 'ok',
                    'message' => 'Dung lượng đĩa còn ' . round($freePercentage, 1) . '%'
                ];
            } else if ($freePercentage > 5) {
                $response['status'] = 'warning';
                $response['message'] = 'Dung lượng đĩa thấp';
                $response['checks']['disk_space'] = [
                    'status' => 'warning',
                    'message' => 'Dung lượng đĩa còn ' . round($freePercentage, 1) . '% - cần giải phóng'
                ];
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Dung lượng đĩa cực thấp';
                $response['checks']['disk_space'] = [
                    'status' => 'error',
                    'message' => 'Dung lượng đĩa còn ' . round($freePercentage, 1) . '% - cần xử lý ngay'
                ];
            }
        }
    } catch (Exception $e) {
        $response['checks']['disk_space'] = [
            'status' => 'unknown',
            'message' => 'Không thể kiểm tra dung lượng đĩa'
        ];
    }

    // Check PHP configuration
    try {
        $phpVersion = phpversion();
        $memoryLimit = ini_get('memory_limit');
        $maxExecutionTime = ini_get('max_execution_time');
        
        $response['checks']['php'] = [
            'status' => 'ok',
            'message' => "PHP {$phpVersion}, Memory: {$memoryLimit}, Max time: {$maxExecutionTime}s"
        ];
    } catch (Exception $e) {
        $response['checks']['php'] = [
            'status' => 'warning',
            'message' => 'Không thể kiểm tra cấu hình PHP'
        ];
    }

    // Check if uploads directory is writable
    try {
        $uploadsDir = realpath('../../public/uploads');
        if (is_dir($uploadsDir) && is_writable($uploadsDir)) {
            $response['checks']['uploads'] = [
                'status' => 'ok',
                'message' => 'Thư mục uploads có thể ghi'
            ];
        } else {
            $response['status'] = 'warning';
            $response['message'] = 'Thư mục uploads không thể ghi';
            $response['checks']['uploads'] = [
                'status' => 'warning',
                'message' => 'Thư mục uploads không thể ghi - cần kiểm tra quyền'
            ];
        }
    } catch (Exception $e) {
        $response['checks']['uploads'] = [
            'status' => 'warning',
            'message' => 'Không thể kiểm tra thư mục uploads'
        ];
    }

    // Check session functionality
    try {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $testKey = 'system_check_' . time();
        $_SESSION[$testKey] = 'test';
        
        if (isset($_SESSION[$testKey])) {
            unset($_SESSION[$testKey]);
            $response['checks']['sessions'] = [
                'status' => 'ok',
                'message' => 'Sessions đang hoạt động'
            ];
        } else {
            throw new Exception('Session test failed');
        }
    } catch (Exception $e) {
        $response['status'] = 'warning';
        $response['checks']['sessions'] = [
            'status' => 'warning',
            'message' => 'Có vấn đề với sessions: ' . $e->getMessage()
        ];
    }

    // Overall health assessment
    $errorCount = 0;
    $warningCount = 0;
    
    foreach ($response['checks'] as $check) {
        if ($check['status'] === 'error') {
            $errorCount++;
        } elseif ($check['status'] === 'warning') {
            $warningCount++;
        }
    }
    
    // Update overall status based on individual checks
    if ($errorCount > 0) {
        $response['status'] = 'error';
        $response['message'] = "Hệ thống có {$errorCount} lỗi nghiêm trọng";
    } elseif ($warningCount > 0) {
        $response['status'] = 'warning';
        $response['message'] = "Hệ thống có {$warningCount} cảnh báo";
    }

    echo json_encode($response);

} catch (Exception $e) {
    // This catch block handles exceptions thrown explicitly by the checks within this script.
    // Fatal errors or uncaught exceptions outside this try-catch are handled by the global error handler.
    
    // It's good practice to log the exception details even if we send a JSON response
    log_exception($e, [
        'endpoint' => 'system_status',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
    ]);

    if (!headers_sent()) {
        http_response_code(500);
        // Ensure Content-Type is application/json for this specific error response
        // The global handler might also set this, but being explicit here is safe.
        header('Content-Type: application/json; charset=utf-8');
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kiểm tra hệ thống: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'checks' => $response['checks'] ?? [] // Include any checks that might have run before the error
    ]);
}
?>
