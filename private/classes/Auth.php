<?php
// private/classes/Auth.php
require_once __DIR__ . '/Database.php'; // Ensure Database class is loaded

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
     * Ensures the current user is authenticated and has the required permission.
     * If not, it sends a 403 JSON response and exits.
     * Checks permission based on the 'role_permissions' table in the database.
     *
     * @param string $requiredPermission The permission string (e.g., 'dashboard', 'user_management') required to access the resource.
     */
    public static function ensureAuthorized(string $requiredPermission) {
        self::ensureAuthenticated(); // Always ensure authenticated first

        if (!isset($_SESSION['admin_role'])) {
            // This case should ideally be caught by ensureAuthenticated if it exits on failure.
            // However, an explicit check for role before querying DB is good.
            $message = 'Vai trò người dùng không được xác định hoặc phiên làm việc không hợp lệ.';
            if (function_exists('api_forbidden')) {
                api_forbidden($message);
            } elseif (function_exists('api_error')) {
                api_error($message, 403);
            } else {
                if (php_sapi_name() !== 'cli' && !headers_sent()) {
                    header('Content-Type: application/json');
                }
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => $message]);
                exit();
            }
        }

        $userRole = $_SESSION['admin_role'];

        if (!self::hasPermission($userRole, $requiredPermission)) {
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
     * Checks if a role has a specific permission based on the database.
     *
     * @param string $role The role to check.
     * @param string $permission The permission to check.
     * @return bool True if the role has the permission, false otherwise.
     */
    private static function hasPermission(string $role, string $permission): bool {
        try {
            // Ensure Database class is loaded (handled by autoloader or require statements elsewhere)
            // e.g., require_once __DIR__ . '/Database.php'; if not autoloaded.
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT 1 FROM `role_permissions` WHERE `role` = :role AND `permission` = :permission AND `allowed` = 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->bindParam(':permission', $permission, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            // Log the error for debugging purposes. Do not expose to the user.
            error_log("Database error in Auth::hasPermission: " . $e->getMessage());
            // In case of a database error, deny permission for security.
            return false;
        } catch (Exception $e) {
            // Catch any other exceptions (e.g., if Database class is not found)
            error_log("Error in Auth::hasPermission: " . $e->getMessage());
            return false;
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

    /**
     * Checks if the current user has a specific permission without enforcing.
     * @param string $permission The permission to check.
     * @return bool True if the user has the permission.
     */
    public static function can(string $permission): bool {
        self::ensureSessionStarted();
        if (!isset($_SESSION['admin_role'])) {
            return false;
        }
        // Access private hasPermission via self
        return self::hasPermission($_SESSION['admin_role'], $permission);
    }
}
?>