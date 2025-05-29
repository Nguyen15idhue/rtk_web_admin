<?php

require_once __DIR__ . '/../config/message.php';
require_once __DIR__ . '/../config/paths.php';
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
            ':notify_content' => $logData[':notify_content'] ?? null, // Map notify_content to notify_content
            ':ip_address'     => $logData[':ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent'     => $logData[':user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // Log error or handle as needed
            error_log("Error adding activity log: " . $e->getMessage());
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

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
}
