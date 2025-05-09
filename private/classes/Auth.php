<?php
// private/classes/Auth.php

class Auth {

    private static function ensureSessionStarted() {
        if (session_status() == PHP_SESSION_NONE) {
            // Attempt to start session if not already started.
            // This is crucial for accessing $_SESSION variables.
            // Suppress errors if headers already sent, as session_start() would warn.
            @session_start();
        }
    }

    /**
     * Ensures the current user is authenticated.
     * If not, it sends a 401 JSON response and exits.
     * Assumes api_error() function is available for consistent error responses.
     */
    public static function ensureAuthenticated() {
        self::ensureSessionStarted();
        if (!isset($_SESSION['admin_id'])) {
            if (function_exists('api_error')) {
                api_error('Xác thực không thành công. Vui lòng đăng nhập lại.', 401);
            } else {
                // Fallback if api_error is not available
                if (php_sapi_name() !== 'cli' && !headers_sent()) {
                    header('Content-Type: application/json');
                }
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Xác thực không thành công. Vui lòng đăng nhập lại.']);
                exit();
            }
        }
    }

    /**
     * Ensures the current user is authenticated and has one of the allowed roles.
     * If not, it sends a 403 JSON response and exits.
     * Assumes api_forbidden() or api_error() functions are available.
     *
     * @param array $allowedRoles An array of roles that are allowed to access the resource.
     */
    public static function ensureAuthorized(array $allowedRoles) {
        self::ensureAuthenticated(); // Always ensure authenticated first

        if (empty($allowedRoles)) { // If no specific roles are required, just being authenticated is enough.
            return;
        }

        if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], $allowedRoles, true)) {
            $message = 'Bạn không có quyền truy cập tài nguyên này.';
            if (function_exists('api_forbidden')) {
                api_forbidden($message);
            } elseif (function_exists('api_error')) {
                api_error($message, 403);
            } else {
                // Fallback if helper functions are not available
                if (php_sapi_name() !== 'cli' && !headers_sent()) {
                    header('Content-Type: application/json');
                }
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => $message]);
                exit();
            }
        }
    }

    /**
     * Checks if the current user is authenticated.
     * @return bool True if authenticated, false otherwise.
     */
    public static function isAuthenticated(): bool {
        self::ensureSessionStarted();
        return isset($_SESSION['admin_id']);
    }

    /**
     * Checks if the current user has a specific role.
     * @param string $role The role to check.
     * @return bool True if the user has the role, false otherwise.
     */
    public static function hasRole(string $role): bool {
        self::ensureSessionStarted();
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === $role;
    }
}
?>