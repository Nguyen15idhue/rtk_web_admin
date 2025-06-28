<?php

// Utility functions can be added here

// Example: Sanitize output
function escape_html($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formats a number as Vietnamese currency.
 *
 * @param float|int|string $amount The amount to format.
 * @param string $currency The currency symbol (default: 'đ').
 * @return string The formatted currency string.
 */
function format_currency($amount, string $currency = 'đ'): string {
    if (!is_numeric($amount)) {
        return 'N/A';
    }
    return number_format((float)$amount, 0, ',', '.') . $currency;
}

/**
 * Formats a datetime string.
 *
 * @param string|null $datetime_string The datetime string to format.
 * @param string $format The desired output format (default: 'd/m/y H:i').
 * @return string The formatted datetime string or 'N/A' on failure.
 */
function format_datetime(?string $datetime_string, string $format = 'd/m/y H:i'): string {
    if (empty($datetime_string)) {
        return 'N/A';
    }
    try {
        $date = new DateTime($datetime_string);
        // Consider timezone if necessary: $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
        return $date->format($format);
    } catch (Exception $e) {
        // Log error if needed: error_log("Error formatting date: {$datetime_string} - " . $e->getMessage());
        return 'N/A';
    }
}

/**
 * Formats a date string into 'd/m/Y' format.
 *
 * @param string|null $date_string The date string to format.
 * @return string The formatted date or '-' if input is empty/invalid.
 */
function format_date(?string $date_string): string {
    if (empty($date_string) || $date_string === '0000-00-00' || $date_string === '0000-00-00 00:00:00') {
        return '-';
    }
    try {
        $date = new DateTime($date_string);
        return $date->format('d/m/Y'); // Format as Day/Month/Year
    } catch (Exception $e) {
        error_log("Error formatting date '{$date_string}': " . $e->getMessage());
        return '-'; // Return '-' on formatting failure
    }
}

/**
 * Generic function to generate an HTML status badge based on provided mapping.
 *
 * @param string|null $status The status key.
 * @param array $map Associative array mapping status to ['class'=> CSS class, 'text'=> label].
 * @return string HTML span element for the badge.
 */
function render_status_badge(?string $status, array $map): string {
    $key = strtolower($status ?? '');
    if (isset($map[$key])) {
        $class = $map[$key]['class'];
        $text = $map[$key]['text'];
    } else {
        $class = 'badge-gray';
        $text = 'Không xác định';
    }
    return '<span class="status-badge ' . $class . '">' . $text . '</span>';
}

/**
 * Generic function to get a status badge by category.
 *
 * @param string $type Category key in mappings (e.g., 'account', 'withdrawal').
 * @param string|null $status Status to lookup.
 * @return string HTML badge.
 */
function get_status_badge(string $type, ?string $status): string {
    // Load the status badge maps from the dedicated config file
    static $loadedStatusBadgeMaps = null;
    if ($loadedStatusBadgeMaps === null) {
        // Ensure the path is correct relative to functions.php
        $configPath = __DIR__ . '/../config/status_badge_maps.php';
        if (file_exists($configPath)) {
            $loadedStatusBadgeMaps = require $configPath;
        } else {
            // Fallback or error handling if the config file is missing
            error_log("Error: Status badge map file not found at " . $configPath);
            $loadedStatusBadgeMaps = []; // Return empty map to avoid further errors
        }
    }
    
    $map = $loadedStatusBadgeMaps[$type] ?? [];
    return render_status_badge($status, $map);
}

/**
 * Get display text for voucher type.
 *
 * @param string $type Voucher type key.
 * @return string Localized voucher type text.
 */
function get_voucher_type_display(string $type): string {
    switch ($type) {
        case 'fixed_discount':
            return 'Giảm cố định';
        case 'percentage_discount':
            return 'Giảm phần trăm';
        case 'extend_duration':
            return 'Tặng tháng';
        default:
            return 'Không xác định';
    }
}

// Load account‐related helper functions
require_once __DIR__ . '/account_helpers.php';

/**
 * Sends a JSON response with an appropriate HTTP status code.
 *
 * @param mixed $data The data to encode as JSON.
 * @param int $statusCode The HTTP status code to send (default: 200).
 */
function send_json_response($data, int $statusCode = 200): void {
    // Ensure headers are not already sent
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
    } else {
        // Log an error if headers are already sent, as we can't set them now.
        error_log("Warning: Headers already sent. Cannot set Content-Type or status code for JSON response.");
    }
    echo json_encode($data);
    exit; // Terminate script execution after sending the response
}

/**
 * Sends a consistent API response.
 *
 * @param mixed $data The main payload.
 * @param string $message The response message.
 * @param int $statusCode The HTTP status code.
 * @param array $errors Detailed errors if any.
 * @param array $meta Additional metadata.
 */
function api_response($data = null, string $message = '', int $statusCode = 200, array $errors = [], array $meta = []): void {
    $envelope = [
        'success' => $statusCode >= 200 && $statusCode < 300,
        'message' => $message,
        'data'    => $data,
        'errors'  => $errors,
        'meta'    => (object)$meta,
    ];
    send_json_response($envelope, $statusCode);
}

/**
 * Sends a successful API response.
 *
 * @param mixed $data The main payload.
 * @param string $message The success message.
 * @param int $statusCode The HTTP status code.
 * @param array $meta Additional metadata.
 */
function api_success($data = null, string $message = 'Cập nhật thành công', int $statusCode = 200, array $meta = []): void {
    api_response($data, $message, $statusCode, [], $meta);
}

/**
 * Sends an error API response.
 *
 * @param string $message The error message.
 * @param int $statusCode The HTTP status code.
 * @param array $errors Detailed errors if any.
 * @param array $meta Additional metadata.
 */
function api_error(string $message = 'Lỗi', int $statusCode = 400, array $errors = [], array $meta = []): void {
    api_response(null, $message, $statusCode, $errors, $meta);
}

/**
 * Sends a 403 Forbidden API response with standard envelope.
 *
 * @param string $message The error message (default: 'Forbidden').
 * @param array  $errors  Detailed errors if any.
 */
function api_forbidden(string $message = 'Bạn không có quyền truy cập vào tài nguyên này', array $errors = []): void {
    api_response(null, $message, 403, $errors);
}

/**
 * Ghi mới phiên vào DB.
 */
function recordSession(int $userId): void {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO ".USER_SESSIONS_TABLE." (user_id, session_id, ip_address, user_agent, created_at, last_activity, is_active)
                          VALUES (?, ?, ?, ?, NOW(), NOW(), 1)");
    $stmt->execute([
        $userId,
        session_id(),
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

/**
 * Đánh dấu phiên inactive.
 */
function deactivateSession(string $sessionId): void {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE ".USER_SESSIONS_TABLE." SET is_active = 0 WHERE session_id = ?");
    $stmt->execute([$sessionId]);
}

/**
 * Kiểm tra phiên có active không, nếu không redirect logout.
 */
function validateSession(int $userId, string $sessionId): void {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT is_active FROM ".USER_SESSIONS_TABLE." WHERE user_id = ? AND session_id = ? LIMIT 1");
    $stmt->execute([$userId, $sessionId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$row || !$row['is_active']) {
        // tự động logout
        deactivateSession($sessionId);
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'public/pages/auth/admin_login.php');
        exit;
    }
    // cập nhật last_activity
    $upd = $db->prepare("UPDATE ".USER_SESSIONS_TABLE." SET last_activity = NOW() WHERE session_id = ?");
    $upd->execute([$sessionId]);
}

/**
 * Include shared logging helpers instead of redefining functions
 */
try {
    require_once __DIR__ . '/logger_helpers.php';
} catch (Exception $e) {
    // Fallback if helpers not found
}

// Add more helper functions as needed...

?>