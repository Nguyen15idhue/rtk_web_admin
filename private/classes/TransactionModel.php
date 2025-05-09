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

    /**
     * Hoàn tác gia hạn: trừ lại thời gian đã cộng cho survey_account
     */
    public function adjustAccountTimesForRevert(int $rid, int $hid): bool {
        // Tính số ngày đã được cộng thêm từ registration
        $sqlDays  = "SELECT DATEDIFF(end_time, start_time) AS days 
                     FROM registration 
                     WHERE id = :rid";
        $stmtDays = $this->db->prepare($sqlDays);
        $stmtDays->execute([':rid' => $rid]);
        $days = (int)$stmtDays->fetchColumn();

        if ($days <= 0) {
            return false;
        }

        // Điều chỉnh lại start_time và end_time trên survey_account
        $sql = "
          UPDATE survey_account
          SET
            end_time   = DATE_SUB(end_time, INTERVAL :days DAY),
            start_time = LEAST(NOW(), DATE_SUB(end_time, INTERVAL 1 DAY)),
            updated_at = NOW()
          WHERE registration_id = :rid
            AND deleted_at IS NULL
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':days' => $days, ':rid' => $rid]);
    }

    /**
     * Lấy toàn bộ dữ liệu transaction để export Excel
     */
    public function getAllDataForExport(): array {
        $sql = "
            SELECT
                r.id AS registration_id,
                u.email AS user_email,
                p.name AS package_name,
                th.amount,
                th.created_at AS request_date,
                th.status AS registration_status
            FROM transaction_history th
            JOIN registration r ON th.registration_id = r.id
            JOIN user u ON r.user_id = u.id
            JOIN package p ON r.package_id = p.id
            ORDER BY th.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy dữ liệu các transaction có ID nằm trong $ids để export Excel
     *
     * @param array $ids danh sách registration_id cần export
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "
            SELECT
                r.id AS registration_id,
                u.email AS user_email,
                p.name AS package_name,
                th.amount,
                th.created_at AS request_date,
                th.status AS registration_status
            FROM transaction_history th
            JOIN registration r ON th.registration_id = r.id
            JOIN user u ON r.user_id = u.id
            JOIN package p ON r.package_id = p.id
            WHERE r.id IN ({$placeholders})
            ORDER BY th.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __destruct() {
        Database::getInstance()->close();
    }
}
