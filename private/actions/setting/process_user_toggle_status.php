<?php
session_start();
// Set header immediately to ensure JSON output even on early errors
header('Content-Type: application/json');

// Correct the paths relative to the current file's directory (private/actions/setting)
require_once __DIR__ . '/../../config/database.php'; // Go up two levels to 'private', then into 'config'
require_once __DIR__ . '/../../classes/Database.php'; // Go up two levels to 'private', then into 'classes'

$response = ['success' => false, 'message' => 'An unexpected error occurred.']; // Default response

try {
    // Authorization Check (Admin only)
    if (!isset($_SESSION['admin_id']) || !in_array($_SESSION['admin_role'] ?? '', ['admin', 'admin'])) {
        http_response_code(403);
        $response['message'] = 'Unauthorized access.';
        echo json_encode($response);
        exit;
    }

    // Input Validation
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        $response['message'] = 'Invalid request method.';
        echo json_encode($response);
        exit;
    }

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    // Remove the deprecated filter. The value is validated below.
    $action = filter_input(INPUT_POST, 'action'); // No filter needed here, validated below

    if (!$user_id || !$action || !in_array($action, ['enable', 'disable'])) {
        http_response_code(400);
        $response['message'] = 'Invalid or missing parameters (user_id, action).';
        echo json_encode($response);
        exit;
    }

    // Prevent admin from disabling themselves (optional safeguard)
    // if ($user_id == $_SESSION['admin_id'] && $action == 'disable') {
    //     http_response_code(400);
    //     $response['message'] = 'Cannot disable your own account.';
    //     echo json_encode($response);
    //     exit;
    // }

    $db = Database::getInstance();;
    $conn = $db->getConnection();

    if (!$conn) {
        http_response_code(500);
        $response['message'] = 'Database connection failed.';
        error_log("Database connection failed in process_user_toggle_status.php");
        echo json_encode($response);
        exit;
    }

    $conn->beginTransaction();

    // Determine the value for deleted_at
    $deleted_at_value = ($action === 'disable') ? date('Y-m-d H:i:s') : null;

    $sql = "UPDATE user SET deleted_at = :deleted_at, updated_at = NOW() WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':deleted_at', $deleted_at_value, $deleted_at_value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Cập nhật trạng thái người dùng thành công.';
            // TODO: Add activity logging here if needed
            // log_activity($_SESSION['admin_id'], $action . '_user', 'user', $user_id);
        } else {
            // User ID might not exist, or status was already the desired state
            $conn->rollBack(); // Rollback even if no rows affected, just to be safe
            http_response_code(404); // Or 400 depending on desired behavior
            $response['message'] = 'User not found or status already updated.';
        }
    } else {
        $conn->rollBack();
        http_response_code(500);
        $response['message'] = 'Failed to update user status.';
        error_log("Failed to execute user status update for user ID: " . $user_id . " - Error: " . implode(":", $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    // Catch database-related errors
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    $response['message'] = 'Database error during status update.';
    error_log("PDOException in process_user_toggle_status.php: " . $e->getMessage());

} catch (Exception $e) {
    // Catch any other general errors
    http_response_code(500);
    $response['message'] = 'An unexpected error occurred: ' . $e->getMessage();
    error_log("Exception in process_user_toggle_status.php: " . $e->getMessage());

} finally {
    // Ensure connection is closed
    if (isset($conn)) {
        $conn = null;
    }
    // Always output the JSON response
    echo json_encode($response);
}
?>
