<?php
// Include necessary files
$bootstrap_data = require_once __DIR__ . '/../../private/core/page_bootstrap.php';
$pdo = $bootstrap_data['db']; // This is the PDO connection

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Handle POST requests for marking notifications as read
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['action'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        // Initialize session storage for read notifications if not exists
        if (!isset($_SESSION['read_notifications'])) {
            $_SESSION['read_notifications'] = [];
        }

        $adminId = $_SESSION['admin_id'];
        
        switch ($input['action']) {
            case 'mark_read':
                if (isset($input['notification_id'])) {
                    // Store in session that this notification is read for this user
                    $key = $adminId . '_' . $input['notification_id'];
                    $_SESSION['read_notifications'][$key] = time();
                    
                    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Missing notification_id']);
                }
                break;
                
            case 'mark_all_read':
                // Mark all current notifications as read
                $notificationIds = [
                    'pending_invoices',
                    'inactive_stations', 
                    'new_support',
                    'new_users',
                    'pending_payments', // Added
                    'pending_withdrawals' // Added
                ];
                
                foreach ($notificationIds as $notificationId) {
                    $key = $adminId . '_' . $notificationId;
                    $_SESSION['read_notifications'][$key] = time();
                }
                
                echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
        }
        
    } catch (Exception $e) {
        error_log("Notifications POST error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error']);
    }
    exit;
}

// Handle GET requests for retrieving notifications
try {
    $notifications = [];
    $adminId = $_SESSION['admin_id'];

    // Initialize session storage for read notifications if not exists
    if (!isset($_SESSION['read_notifications'])) {
        $_SESSION['read_notifications'] = [];
    }

    // Helper function to check if notification is read
    function isNotificationRead($notificationId, $adminId) {
        $key = $adminId . '_' . $notificationId;
        return isset($_SESSION['read_notifications'][$key]);
    }

    function formatRelativeTime($timestamp) {
        if ($timestamp === null) {
            return 'Không rõ thời gian'; // Undetermined time
        }
        $eventTime = new DateTime($timestamp);
        $now = new DateTime();
        $diffSeconds = $now->getTimestamp() - $eventTime->getTimestamp();

        if ($diffSeconds < 5) { // less than 5 seconds
            return 'Vừa xong';
        } elseif ($diffSeconds < 60) { // less than 1 minute
            return $diffSeconds . ' giây trước';
        } elseif ($diffSeconds < 3600) { // less than 1 hour
            $minutes = round($diffSeconds / 60);
            return $minutes . ' phút trước';
        } elseif ($diffSeconds < 86400) { // less than 1 day
            $hours = round($diffSeconds / 3600);
            return $hours . ' giờ trước';
        } elseif ($diffSeconds < 172800) { // less than 2 days (yesterday)
            return 'Hôm qua lúc ' . $eventTime->format('H:i');
        } else {
            // Check if it\'s the same year
            if ($eventTime->format('Y') === $now->format('Y')) {
                return $eventTime->format('d/m \\l\\ú\\c H:i');
            } else {
                return $eventTime->format('d/m/Y \\l\\ú\\c H:i');
            }
        }
    }

    // Get pending invoice approvals
    $pendingInvoicesStmt = $pdo->prepare("
        SELECT COUNT(*) as count, MAX(created_at) as latest_event_time
        FROM invoice 
        WHERE status = 'pending'
    ");
    $pendingInvoicesStmt->execute();
    $pendingInvoicesData = $pendingInvoicesStmt->fetch(PDO::FETCH_ASSOC);
    $pendingInvoicesCount = $pendingInvoicesData['count'];
    $latestInvoiceTime = $pendingInvoicesData['latest_event_time'];

    if ($pendingInvoicesCount > 0) {
        $notifications[] = [
            'id' => 'pending_invoices',
            'title' => 'Hóa đơn cần phê duyệt',
            'message' => "Có <strong>{$pendingInvoicesCount}</strong> hóa đơn đang chờ phê duyệt.",
            'time' => formatRelativeTime($latestInvoiceTime),
            'type' => 'warning',
            'unread' => !isNotificationRead('pending_invoices', $adminId),
            'url' => '/public/pages/invoice/invoice_review.php'
        ];
    }

    // Get inactive stations
    $inactiveStationsStmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM station 
        WHERE status = '2' OR status = '3'
    ");
    $inactiveStationsStmt->execute();
    $inactiveStationsData = $inactiveStationsStmt->fetch(PDO::FETCH_ASSOC);
    $inactiveStationsCount = $inactiveStationsData['count'];

    if ($inactiveStationsCount > 0) {
        $notifications[] = [
            'id' => 'inactive_stations',
            'title' => 'Trạm không hoạt động',
            'message' => "Có <strong>{$inactiveStationsCount}</strong> trạm đang không hoạt động.",
            'time' => '5 phút trước',
            'type' => 'warning',
            'unread' => !isNotificationRead('inactive_stations', $adminId),
            'url' => '/public/pages/station/station_management.php'
        ];
    }

    // Get recent support requests
    $supportRequestsStmt = $pdo->prepare("
        SELECT COUNT(*) as count, MAX(created_at) as latest_event_time
        FROM support_requests 
        WHERE status = 'open' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $supportRequestsStmt->execute();
    $supportRequestsData = $supportRequestsStmt->fetch(PDO::FETCH_ASSOC);
    $recentSupportCount = $supportRequestsData['count'];
    $latestSupportRequestTime = $supportRequestsData['latest_event_time'];

    if ($recentSupportCount > 0) {
        $notifications[] = [
            'id' => 'new_support',
            'title' => 'Yêu cầu hỗ trợ mới',
            'message' => "Có <strong>{$recentSupportCount}</strong> yêu cầu hỗ trợ mới trong 24 giờ qua.",
            'time' => formatRelativeTime($latestSupportRequestTime),
            'type' => 'info',
            'unread' => !isNotificationRead('new_support', $adminId),
            'url' => '/public/pages/support/support_management.php'
        ];
    }

    // Get new user registrations today
    $newUsersStmt = $pdo->prepare("
        SELECT COUNT(*) as count, MAX(created_at) as latest_event_time
        FROM user 
        WHERE DATE(created_at) = CURDATE()
    ");
    $newUsersStmt->execute();
    $newUsersData = $newUsersStmt->fetch(PDO::FETCH_ASSOC);
    $newUsersCount = $newUsersData['count'];
    $latestUserRegTime = $newUsersData['latest_event_time'];

    if ($newUsersCount > 0) {
        $notifications[] = [
            'id' => 'new_users',
            'title' => 'Người dùng mới đăng ký',
            'message' => "Có <strong>{$newUsersCount}</strong> người dùng mới đăng ký hôm nay.",
            'time' => formatRelativeTime($latestUserRegTime),
            'type' => 'success',
            'unread' => !isNotificationRead('new_users', $adminId),
            'url' => '/public/pages/user/user_management.php'
        ];
    }

    // Get pending payment orders (assuming 'transaction_history' table and 'pending' status)
    $pendingPaymentsStmt = $pdo->prepare("
        SELECT COUNT(*) as count, MAX(created_at) as latest_event_time
        FROM transaction_history 
        WHERE status = 'pending' 
        AND DATE(created_at) = CURDATE() 
    ");
    $pendingPaymentsStmt->execute();
    $pendingPaymentsData = $pendingPaymentsStmt->fetch(PDO::FETCH_ASSOC);
    $pendingPaymentsCount = $pendingPaymentsData['count'];
    $latestPaymentTime = $pendingPaymentsData['latest_event_time'];

    if ($pendingPaymentsCount > 0) {
        $notifications[] = [
            'id' => 'pending_payments',
            'title' => 'Đơn thanh toán mới',
            'message' => "Có <strong>{$pendingPaymentsCount}</strong> đơn thanh toán mới cần xử lý.",
            'time' => formatRelativeTime($latestPaymentTime), // Adjust time as needed
            'type' => 'info',
            'unread' => !isNotificationRead('pending_payments', $adminId),
            'url' => '/public/pages/purchase/invoice_management.php' // Adjust URL as needed
        ];
    }

    // Get pending withdrawal requests (assuming 'withdrawal_requests' table and 'pending' status)
    $pendingWithdrawalsStmt = $pdo->prepare("
        SELECT COUNT(*) as count, MAX(created_at) as latest_event_time
        FROM withdrawal_request 
        WHERE status = 'pending'
        AND DATE(created_at) = CURDATE() 
    ");
    $pendingWithdrawalsStmt->execute();
    $pendingWithdrawalsData = $pendingWithdrawalsStmt->fetch(PDO::FETCH_ASSOC);
    $pendingWithdrawalsCount = $pendingWithdrawalsData['count'];
    $latestWithdrawalTime = $pendingWithdrawalsData['latest_event_time'];

    if ($pendingWithdrawalsCount > 0) {
        $notifications[] = [
            'id' => 'pending_withdrawals',
            'title' => 'Yêu cầu rút tiền mới',
            'message' => "Có <strong>{$pendingWithdrawalsCount}</strong> yêu cầu rút tiền mới cần xử lý.",
            'time' => formatRelativeTime($latestWithdrawalTime), // Adjust time as needed
            'type' => 'warning',
            'unread' => !isNotificationRead('pending_withdrawals', $adminId),
            'url' => '/public/pages/referral/referral_management.php?tab=withdrawals' // Adjust URL as needed
        ];
    }

    // Count unread notifications
    $unreadCount = array_reduce($notifications, function($count, $notification) {
        return $count + ($notification['unread'] ? 1 : 0);
    }, 0);

    echo json_encode([
        'success' => true, // Added success flag
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);

} catch (PDOException $e) {
    error_log("Notifications API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load notifications due to database error.']); // Added success flag
} catch (Exception $e) {
    error_log("Notifications API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load notifications due to a server error.']); // Added success flag
}
?>
