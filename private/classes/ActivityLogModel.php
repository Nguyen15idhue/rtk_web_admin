<?php

class ActivityLogModel {

    /**
     * Adds a new log entry to the activity_logs table.
     *
     * @param PDO $db The database connection object.
     * @param array $logData An associative array containing the log data.
     * Expected keys:
     *   ':user_id' (nullable) - The ID of the user performing the action (or null).
     *   ':action' - A string describing the action (e.g., 'invoice_reverted').
     *   ':entity_type' - The type of entity affected (e.g., 'invoice').
     *   ':entity_id' (nullable) - The ID of the entity affected.
     *   ':old_values' (nullable) - JSON string of old values.
     *   ':new_values' (nullable) - JSON string of new values.
     *   ':notify_content' (nullable) - A human-readable notify_content of the log (will be stored in 'notify_content' column).
     *   ':ip_address' (nullable) - IP address of the user.
     *   ':user_agent' (nullable) - User agent of the user.
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
}
