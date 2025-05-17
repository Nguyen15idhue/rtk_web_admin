<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class InvoiceModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách hoá đơn với lọc và phân trang
     */
    public function getAll(array $filters = [], int $limit = 0, int $offset = 0): array {
        // build base query with joins to get related info
        $sql = "
            SELECT
                inv.id AS invoice_id,
                inv.transaction_history_id,
                inv.created_at AS request_date,
                inv.status,
                inv.invoice_file,
                inv.rejected_reason,
                u.email AS user_email,
                p.name AS package_name
            FROM invoice inv
            JOIN transaction_history th ON inv.transaction_history_id = th.id
            JOIN registration r ON th.registration_id = r.id
            JOIN user u ON r.user_id = u.id
            JOIN package p ON r.package_id = p.id
        ";
        $where   = [];
        $params  = [];

        // filters
        if (!empty($filters['status'])) {
            $where[]        = 'inv.status = ?';
            $params[]       = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[]        = 'DATE(inv.created_at) >= ?';
            $params[]       = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]        = 'DATE(inv.created_at) <= ?';
            $params[]       = $filters['date_to'];
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        // pagination
        if ($limit > 0) {
            $sql .= ' ORDER BY inv.created_at DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }

        // prepare, execute, fetch
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy một hoá đơn theo ID
     */
    public function getOne(int $id): ?array {
        $sql = "SELECT * FROM invoice WHERE id=? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Tạo hoá đơn mới
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO invoice(transaction_history_id, status, created_at)
                VALUES(?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['transaction_history_id'],
            $data['status'] ?? 'pending'
        ]);
    }

    /**
     * Cập nhật thông tin hoá đơn (trừ file)
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE invoice
                   SET status = ?, rejected_reason = ?
                 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['status'],
            $data['rejected_reason'] ?? null,
            $id
        ]);
    }

    /**
     * Xoá hoá đơn
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM invoice WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Gắn file PDF cho hoá đơn (chạy trong transaction nếu cần)
     */
    public function attachFile(int $id, string $fileName): bool {
        $sql = "UPDATE invoice
                   SET invoice_file = ?, status = 'approved'
                 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fileName, $id]);
    }

    /**
     * Get the customer (user) ID associated with this invoice.
     */
    public function getCustomerId(int $invoiceId): ?int {
        $sql = "
            SELECT r.user_id
            FROM invoice inv
            JOIN transaction_history th ON inv.transaction_history_id = th.id
            JOIN registration r ON th.registration_id = r.id
            WHERE inv.id = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$invoiceId]);
        $userId = $stmt->fetchColumn();
        return $userId !== false ? (int) $userId : null;
    }
}
