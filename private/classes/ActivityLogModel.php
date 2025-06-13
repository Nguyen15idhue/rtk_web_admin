<?php

require_once __DIR__ . '/../config/message.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../config/mail.php';  // mail config
// Load Composer autoloader for PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';
// Import PHPMailer classes into global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// dashboard helpers
require_once __DIR__ . '/../utils/dashboard_helpers.php';

class ActivityLogModel {
    /**
     * Adds an activity log entry to the database.
     *
     * @param PDO $db The PDO database connection.
     * @param array $logData The log data to insert.
     * @return bool True on success, false on failure.
     */
    public static function addLog(PDO $db, array $logData): bool {
        static $sentNotifications = []; // prevent duplicate email queueing per request
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, old_values, new_values, notify_content, ip_address, user_agent, created_at)
                VALUES (:user_id, :action, :entity_type, :entity_id, :old_values, :new_values, :notify_content, :ip_address, :user_agent, NOW())";

        // Ensure all expected keys are present, providing defaults for optional ones
        $params = [
            ':user_id'        => $logData[':user_id'] ?? null,
            ':action'         => $logData[':action'] ?? 'unknown_action',
            ':entity_type'    => $logData[':entity_type'] ?? 'unknown_entity',
            ':entity_id'      => $logData[':entity_id'] ?? null,
            ':old_values'     => $logData[':old_values'] ?? null,
            ':new_values'     => $logData[':new_values'] ?? null,
            ':notify_content' => $logData[':notify_content'] ?? null,
            ':ip_address'     => $logData[':ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent'     => $logData[':user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];

        try {
            $stmt = $db->prepare($sql);
            $logInserted = $stmt->execute($params);

            // Queue email notification if applicable
            if ($logInserted && !empty($params[':user_id']) && !empty($params[':notify_content'])) {
                $key = ($params[':entity_type'] ?? '') . '_' . ($params[':entity_id'] ?? '') . '_' . ($params[':user_id'] ?? ''); // Make key more specific
                
                if (!isset($sentNotifications[$key])) {
                    // Fetch user email
                    $emailStmt = $db->prepare('SELECT email FROM user WHERE id = :id');
                    $emailStmt->execute([':id' => $params[':user_id']]);
                    $user = $emailStmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                        $to = $user['email'];
                        // Determine dynamic subject based on action style
                        $actionStyles = self::getActionStyles();
                        // use lowercase action key directly
                        $actionKey = strtolower(trim((string)($params[':action'] ?? '')));
                        $style = $actionStyles[$actionKey] ?? $actionStyles['default'];
                        $subject = $style['icon'] . ' ' . $style['title'];
                        
                        $htmlBody = self::createEmailTemplate($params[':notify_content'], $params[':action'] ?? 'unknown_action');
                        $textBody = strip_tags($params[':notify_content']); // Basic plain text version
                        
                        // Insert into email_queue table
                        $queueSql = "INSERT INTO email_queue (recipient_email, subject, html_body, text_body, status, created_at)
                                     VALUES (:recipient_email, :subject, :html_body, :text_body, 'pending', NOW())";
                        $queueStmt = $db->prepare($queueSql);
                        $emailQueued = $queueStmt->execute([
                            ':recipient_email' => $to,
                            ':subject'         => $subject,
                            ':html_body'       => $htmlBody,
                            ':text_body'       => $textBody,
                        ]);

                        if ($emailQueued) {
                            $sentNotifications[$key] = true; // Mark as queued for this request to prevent duplicates
                            error_log("ActivityLogModel: Email for {$to} action {$params[':action']} queued successfully.");
                        } else {
                            error_log("ActivityLogModel: Failed to queue email for {$to}. DB Error: " . implode(", ", $queueStmt->errorInfo()));
                        }
                    }
                }
            }
            return $logInserted;
        } catch (PDOException $e) {
            error_log("Error in ActivityLogModel::addLog: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sends a message to a Telegram chat via bot API.
     *
     * @param string $message The message text to send.
     * @return bool True on success, false on failure.
     */
    public static function sendTelegram(string $message): bool {
        if (!defined('TELEGRAM_BOT_TOKEN') || !defined('TELEGRAM_CHAT_ID') || TELEGRAM_BOT_TOKEN === 'YOUR_TELEGRAM_BOT_TOKEN' || TELEGRAM_CHAT_ID === 'YOUR_TELEGRAM_CHAT_ID') {
            error_log("Telegram API Error: TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID is not defined or not configured.");
            return false;
        }
        $token = TELEGRAM_BOT_TOKEN;
        $chat_id = TELEGRAM_CHAT_ID;
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        // Append system link to message
        $baseUrl = defined('BASE_URL') ? BASE_URL : ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');
        $message .= "\n\n<a href=\"{$baseUrl}\">Mở trang web</a>";
        $data = ['chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML']; // Added parse_mode HTML for better formatting

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Use http_build_query for proper encoding
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Set a connection timeout

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_errno > 0) {
            error_log("Telegram cURL Error ({$curl_errno}): {$curl_error}");
            return false;
        }

        if ($http_code !== 200) {
            error_log("Telegram API Error (HTTP {$http_code}): Response: " . $response);
            return false;
        }

        return true;
    }

    /**
     * Sends a message to a Discord channel via webhook.
     *
     * @param string $message The message text to send.
     * @return bool True on success, false on failure.
     */
    public static function sendDiscord(string $message): bool {
        $webhookUrl = defined('DISCORD_WEBHOOK_URL') ? DISCORD_WEBHOOK_URL : '';

        if (empty($webhookUrl)) {
            error_log("Discord Webhook Error: Webhook URL is not configured.");
            return false;
        }

        $payload = ['content' => $message];
        $data = json_encode($payload);
        // Debug logs for Discord
        error_log("Discord Webhook URL: {$webhookUrl}\n");
        error_log("Discord Payload: {$data}\n");
        echo "Discord Webhook URL: {$webhookUrl}\n";
        echo "Discord Payload: {$data}\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log response for debugging
        error_log("Discord Response HTTP Code: {$http_code}\n");
        error_log("Discord Response Body: {$response}\n");
        echo "Discord Response HTTP Code: {$http_code}\n";
        echo "Discord Response Body: {$response}\n";

        if ($curl_errno > 0) {
            error_log("Discord cURL Error ({$curl_errno}): {$curl_error}");
            return false;
        }

        if ($http_code !== 200 && $http_code !== 204) {
            error_log("Discord API Error (HTTP {$http_code}): Response: " . $response);
            return false;
        }

        return true;
    }

    /**
     * Creates a beautiful HTML email template
     * @param string $content Main content message
     * @param string $action Type of action for styling
     * @return string HTML email content
     */
    private static function createEmailTemplate(string $content, string $action): string {
        // Get base URL for links
        $baseUrl = defined('BASE_URL') ? BASE_URL : 'https://rtk-admin.local/';
        
        // Primary color for email theme
        $primaryColor = '#007bff';
        $primaryGradient = $primaryColor . ' 0%, ' . $primaryColor . '99 50%';
        // Determine action icon and title
        $actionStyles = self::getActionStyles();
        $actionKey = strtolower(trim($action));
        $style = $actionStyles[$actionKey] ?? $actionStyles['default'];
        // Use primaryColor for theme
        $color = $primaryColor;
         $currentDate = date('d/m/Y H:i:s');
         
         return '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo từ RTK Web Admin</title>
    <style>
        body { margin: 0; padding: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, ' . $primaryGradient . '); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .header .icon { font-size: 48px; margin-bottom: 10px; display: block; }
        .content { padding: 30px 20px; }
        .message-box { background-color: #f8f9fa; border-left: 4px solid ' . $color . '; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .message-box p { margin: 0; line-height: 1.6; color: #333; font-size: 16px; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6; }
        .footer p { margin: 5px 0; color: #6c757d; font-size: 14px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: ' . $color . '; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; margin: 15px 0; }
        .btn:hover { background-color: ' . $color . 'dd; }
        .divider { height: 1px; background-color: #dee2e6; margin: 25px 0; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .header, .content, .footer { padding: 20px 15px !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icon">' . $style['icon'] . '</span>
            <h1>' . $style['title'] . '</h1>
        </div>
        
        <div class="content">
            <p>Xin chào,</p>
            
            <div class="message-box">
                <p>' . nl2br(htmlspecialchars($content)) . '</p>
            </div>
            
            <div class="divider"></div>
            
            <p>Để xem chi tiết hoặc thực hiện các thao tác khác, vui lòng truy cập hệ thống:</p>
            
            <div style="text-align: center;">
                <a href="' . USER_URL . '" class="btn" style="color: #ffffff !important;">🌐 Truy cập RTK Web</a>
            </div>
            
            <p style="margin-top: 25px; color: #6c757d; font-size: 14px;">
                <strong>Lưu ý:</strong> Đây là email tự động được gửi từ hệ thống RTK Web Admin. 
                Vui lòng không trả lời email này.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>RTK Web Admin</strong></p>
            <p>Thời gian gửi: ' . $currentDate . '</p>
            <p style="margin-top: 10px; font-size: 12px; color: #adb5bd;">
                © 2025 RTK Web Admin. Bảo lưu mọi quyền.
            </p>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Returns mapping of action styles including color, icon, and title
     */
    private static function getActionStyles(): array {
        return [
            'support_request_updated' => ['color' => '#28a745', 'icon' => '💬', 'title' => 'Hỗ trợ khách hàng'],
            'approve_transaction'    => ['color' => '#28a745', 'icon' => '✅', 'title' => 'Giao dịch được duyệt'],
            'reject_transaction'     => ['color' => '#dc3545', 'icon' => '❌', 'title' => 'Giao dịch bị từ chối'],
            'invoice_sent'           => ['color' => '#007bff', 'icon' => '📄', 'title' => 'Hóa đơn được xử lý'],
            'reject_invoice'         => ['color' => '#dc3545', 'icon' => '❌', 'title' => 'Hóa đơn bị từ chối'],
            'account_updated'        => ['color' => '#17a2b8', 'icon' => '👤', 'title' => 'Tài khoản được cập nhật'],
            'default'                => ['color' => '#6c757d', 'icon' => '🔔', 'title' => 'Thông báo hệ thống'],
        ];
    }
}
