<?php
$bootstrap_data = require_once __DIR__ . '/../../private/core/page_bootstrap.php';
$db = $bootstrap_data['db'];
$base_path = $bootstrap_data['base_path'];
$base_url = $bootstrap_data['base_url'];
$private_layouts_path = $bootstrap_data['private_layouts_path'];

require_once __DIR__ . '/../../private/classes/Logger.php';

// Security OTP check for logs page
$required_otp = '6886';
$otp_session_key = 'logs_page_otp_verified';

// Check if OTP verification is needed
if (!isset($_SESSION[$otp_session_key]) || $_SESSION[$otp_session_key] !== true) {
    // Handle OTP verification
    if (isset($_POST['verify_otp'])) {
        $entered_otp = $_POST['otp'] ?? '';
        if ($entered_otp === $required_otp) {
            $_SESSION[$otp_session_key] = true;
            Logger::info("Xác thực OTP thành công để truy cập trang nhật ký", ['ip' => $_SERVER['REMOTE_ADDR']]);
            // Redirect to avoid form resubmission
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            Logger::warning("Thử xác thực OTP không thành công cho trang nhật ký", ['ip' => $_SERVER['REMOTE_ADDR'], 'entered_otp' => $entered_otp]);
            $otp_error = 'Mã OTP không đúng. Vui lòng thử lại.';
        }
    }
    
    // Show OTP verification form
    if (!isset($_SESSION[$otp_session_key]) || $_SESSION[$otp_session_key] !== true) {
        include $private_layouts_path . 'otp_verification.php';
        exit;
    }
}

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? '';
    
    try {
        switch ($action) {
            case 'get_logs':
                $level = $_GET['level'] ?? null;
                $limit = (int)($_GET['limit'] ?? 100);
                
                // If level is 'all', pass null to get all levels
                if ($level === 'all') {
                    $level = null;
                }
                
                $logs = Logger::getRecentLogs($level, $limit);
                
                echo json_encode([
                    'success' => true,
                    'logs' => $logs
                ]);
                break;
                
            case 'get_stats':
                $stats = Logger::getLogStats();
                
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
                break;
                
            case 'clear_logs':
                $level = $_GET['level'] ?? '';
                
                if (empty($level)) {
                    throw new Exception('Tham số cấp độ là bắt buộc');
                }
                
                // Clear logs by truncating the file
                $logDir = __DIR__ . '/../../private/logs/';
                $today = date('Y-m-d');
                $filename = strtolower($level) . '_' . $today . '.log';
                $filepath = $logDir . $filename;
                
                if (file_exists($filepath)) {
                    file_put_contents($filepath, '');
                    Logger::info("Đã xóa nhật ký $level cho ngày $today", ['admin_action' => true]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => "Đã xóa thành công nhật ký $level hôm nay"
                ]);
                break;
                
            case 'export_logs':
                $level = $_GET['level'] ?? 'all';
                $today = date('Y-m-d');
                
                // Get logs for export
                if ($level === 'all') {
                    $logs = Logger::getRecentLogs(null, 10000); // Get more logs for export
                } else {
                    $logs = Logger::getRecentLogs($level, 10000);
                }
                
                // Set headers for file download
                $filename = "logs_{$level}_{$today}.txt";
                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: no-cache, must-revalidate');
                
                // Export logs
                foreach ($logs as $log) {
                    echo $log['raw'] . "\n";
                }
                exit;
                
            case 'clear_old_logs':
                $logDir = __DIR__ . '/../../private/logs/';
                $files = glob($logDir . '*.log');
                $threshold = time() - 7 * 24 * 60 * 60; // 7 days
                $deletedCount = 0;
                foreach ($files as $file) {
                    if (filemtime($file) < $threshold) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
                Logger::info("Đã xóa {$deletedCount} file nhật ký cũ hơn 7 ngày", ['admin_action' => true]);
                echo json_encode([
                    'success' => true,
                    'message' => "Đã xóa thành công {$deletedCount} file nhật ký cũ hơn 7 ngày"
                ]);
                break;
                
            default:
                throw new Exception('Hành động không hợp lệ');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

$page_title = 'Nhật ký Hệ thống';
$additional_css = ['pages/logs.css'];
?>
<?php include $private_layouts_path . 'admin_header.php'; ?>
<?php include $private_layouts_path . 'admin_sidebar.php'; ?>

<main class="content-wrapper">
    <?php include $private_layouts_path . 'content_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <!-- Log Statistics -->
            <div class="row stats-row mb-4">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <div class="stats-card error-card">
                        <div class="stats-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number" id="error-count">-</div>
                            <div class="stats-label">Lỗi Hôm nay</div>
                            <div class="stats-trend">
                                <span class="trend-indicator">Vấn đề nghiêm trọng</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <div class="stats-card warning-card">
                        <div class="stats-icon">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number" id="warning-count">-</div>
                            <div class="stats-label">Cảnh báo Hôm nay</div>
                            <div class="stats-trend">
                                <span class="trend-indicator">Cần chú ý</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <div class="stats-card info-card">
                        <div class="stats-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number" id="info-count">-</div>
                            <div class="stats-label">Thông tin Hôm nay</div>
                            <div class="stats-trend">
                                <span class="trend-indicator">Hoạt động chung</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                    <div class="stats-card debug-card">
                        <div class="stats-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number" id="debug-count">-</div>
                            <div class="stats-label">Debug Hôm nay</div>
                            <div class="stats-trend">
                                <span class="trend-indicator">Thông tin phát triển</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Viewer -->
            <div class="logs-container">
                <div class="logs-header">
                    <div class="logs-title">
                        <h4 class="mb-0">
                            <i class="fas fa-terminal text-primary"></i>
                            Nhật ký Hệ thống Thời gian Thực
                        </h4>
                        <small class="text-muted">Cập nhật lần cuối: <span id="last-updated">-</span></small>
                    </div>
                    <div class="logs-controls">
                        <!-- Filter Buttons -->
                        <div class="filter-buttons">
                            <div class="btn-group btn-group-sm filter-group" role="group">
                                <button type="button" class="btn btn-outline-primary log-filter active" data-level="all">
                                    <i class="fas fa-list"></i> Tất cả
                                </button>
                                <button type="button" class="btn btn-outline-danger log-filter" data-level="error">
                                    <i class="fas fa-exclamation-triangle"></i> Lỗi
                                </button>
                                <button type="button" class="btn btn-outline-warning log-filter" data-level="warning">
                                    <i class="fas fa-exclamation"></i> Cảnh báo
                                </button>
                                <button type="button" class="btn btn-outline-info log-filter" data-level="info">
                                    <i class="fas fa-info-circle"></i> Thông tin
                                </button>
                                <button type="button" class="btn btn-outline-secondary log-filter" data-level="debug">
                                    <i class="fas fa-code"></i> Debug
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-primary" id="refresh-logs">
                                <i class="fas fa-sync"></i> Làm mới
                            </button>
                            
                            <!-- Clear Actions Group -->
                            <div class="action-group">
                                <button type="button" class="btn btn-sm btn-danger clear-logs" data-level="error" title="Xóa Nhật ký Lỗi">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning clear-logs" data-level="warning" title="Xóa Nhật ký Cảnh báo">
                                    <i class="fas fa-exclamation"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info clear-logs" data-level="info" title="Xóa Nhật ký Thông tin">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary clear-logs" data-level="debug" title="Xóa Nhật ký Debug">
                                    <i class="fas fa-code"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="export-logs" title="Xuất Nhật ký">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="logs-content">
                    <div class="logs-table-wrapper">
                        <table class="table logs-table">
                            <thead>
                                <tr>
                                    <th width="180">
                                        <i class="fas fa-clock"></i> Thời gian
                                    </th>
                                    <th width="120">
                                        <i class="fas fa-layer-group"></i> Cấp độ
                                    </th>
                                    <th>
                                        <i class="fas fa-comment-alt"></i> Thông điệp
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="logs-table-body">
                                <tr class="loading-row">
                                    <td colspan="3" class="text-center">
                                        <div class="loading-spinner">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                        <div class="loading-text">
                                            <span>Đang tải nhật ký hệ thống...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Auto-refresh indicator -->
                    <div class="auto-refresh-indicator">
                        <i class="fas fa-sync-alt"></i>
                        <span>Tự động làm mới mỗi 30 giây</span>
                        <div class="refresh-progress"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script defer src="<?php echo $base_path; ?>public/assets/js/pages/logs.js"></script>
<?php include $private_layouts_path . 'admin_footer.php'; ?>
