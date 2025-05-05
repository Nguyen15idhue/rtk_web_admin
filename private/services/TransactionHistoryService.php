<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\services\TransactionHistoryService.php
declare(strict_types=1);

class TransactionHistoryService {

    /**
     * Updates the status of transaction history records associated with a registration ID.
     * Assumes a transaction history record might exist for the registration.
     *
     * @param PDO $db The database connection object.
     * @param int $registration_id The ID of the registration.
     * @param string $new_status The new status ('pending', 'completed', 'failed', 'refunded', 'rejected').
     * @return bool True on success or if no record found, false on failure.
     * @throws PDOException If a database error occurs.
     */
    public static function updateStatusByRegistrationId(PDO $db, int $registration_id, string $new_status): bool {
        // Validate status
        $valid_statuses = ['pending', 'completed', 'failed', 'refunded', 'rejected'];
        if (!in_array($new_status, $valid_statuses)) {
            error_log("Invalid status provided to TransactionHistoryService: " . $new_status);
            return false; // Or throw an InvalidArgumentException
        }

        try {
            // Find relevant transaction types to update based on the new status
            $target_statuses = [];
            switch ($new_status) {
                case 'completed':
                    $target_statuses = ['pending', 'failed']; // Can complete from pending or failed
                    break;
                case 'failed':
                    $target_statuses = ['pending', 'completed']; // Can fail from pending or completed
                    break;
                case 'pending':
                    $target_statuses = ['completed', 'failed']; // Can revert to pending from completed or failed
                    break;
                case 'refunded':
                    $target_statuses = ['completed']; // Usually refund only completed transactions
                    break;
                case 'rejected':
                    // Allow transition from any status (optional)
                    $target_statuses = ['pending', 'completed', 'failed', 'refunded'];
                    break;
            }

            if (empty($target_statuses)) {
                 error_log("No target statuses defined for new status: " . $new_status);
                 return true; // Or handle as error?
            }

            // Construct the IN clause for target statuses
            $placeholders = implode(',', array_fill(0, count($target_statuses), '?'));
            $sql = "UPDATE transaction_history
                    SET status = ?, updated_at = NOW()
                    WHERE registration_id = ?
                    AND status IN ({$placeholders})";

            $stmt = $db->prepare($sql);

            // Bind parameters
            $params = array_merge([$new_status, $registration_id], $target_statuses);
            $stmt->execute($params);

            // No need to check rowCount strictly, as it's okay if no matching record was found to update.
            // If an error occurred, PDO would throw an exception.
            return true;

        } catch (PDOException $e) {
            // Bổ sung stack trace và context
            error_log(
                "Database error in " . __METHOD__ . 
                ": " . $e->getMessage() . 
                "\nRegistration ID: $registration_id, New status: $new_status" .
                "\nTrace:\n" . $e->getTraceAsString()
            );
            throw $e; // Re-throw the exception to be caught by the caller for transaction rollback
        }
    }

    // Add other methods as needed, e.g., createTransactionHistory(...)
}
?>
