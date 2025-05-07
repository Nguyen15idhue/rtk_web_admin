<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class TransactionModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getHistoryById(int $hid): array|false {
        $sql = "SELECT * FROM transaction_history WHERE id = :id FOR UPDATE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $hid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRegistrationById(int $rid): array|false {
        $sql = "SELECT * FROM registration WHERE id = :id AND deleted_at IS NULL FOR UPDATE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $rid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRegistrationStatus(int $rid, string $status, ?string $reason = null): bool {
        $fields = "status = :st, updated_at = NOW()";
        $params = [':st' => $status, ':id' => $rid];
        if ($reason !== null) {
            $fields .= ", rejection_reason = :rs";
            $params[':rs'] = $reason;
        }
        $sql = "UPDATE registration SET {$fields} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateHistoryStatus(int $hid, string $status): bool {
        $sql = "UPDATE transaction_history SET status = :st, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':st' => $status, ':id' => $hid]);
    }

    public function getSurveyAccounts(int $rid): array {
        $sql = "SELECT * FROM survey_account WHERE registration_id = :id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $rid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all survey accounts for a registration, regardless of their deleted_at status.
     * Used for operations like revert/reject where all associated RTK accounts must be handled.
     *
     * @param int $rid Registration ID.
     * @return array List of survey accounts.
     */
    public function getAllSurveyAccountsForRegistration(int $rid): array {
        $sql = "SELECT id, username_acc FROM survey_account WHERE registration_id = :id"; // Fetch only necessary fields like id
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $rid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function softDeleteAccounts(int $rid): bool {
        $sql = "UPDATE survey_account SET deleted_at=NOW(), updated_at=NOW() WHERE registration_id=:id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $rid]);
    }

    public function resetEndTime(int $rid): bool {
        $sql = "UPDATE registration SET end_time = start_time, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $rid]);
    }

    public function unconfirmPayment(int $hid): bool {
        $sql = "UPDATE transaction_history 
                SET payment_confirmed = 0, payment_confirmed_at = NULL, updated_at = NOW() 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $hid]);
    }

    public function __destruct() {
        Database::getInstance()->close();
    }
}
