<?php
// filepath: private/classes/SupportRequestModel.php
require_once __DIR__ . '/Database.php';

class SupportRequestModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all support requests with optional filters.
     * @param array $filters ['search' => string, 'status' => string, 'category' => string]
     * @return array
     */
    public function getAll(array $filters = []): array {
        $sql = "SELECT sr.*, u.email AS user_email FROM support_requests sr JOIN user u ON sr.user_id = u.id";
        $where = [];
        $params = [];
        if (!empty($filters['search'])) {
            // search across subject, message, and user email using one placeholder
            $where[] = "CONCAT_WS(' ', sr.subject, sr.message, u.email) LIKE :search";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "sr.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = "sr.category = :category";
            $params[':category'] = $filters['category'];
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY sr.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single support request by ID.
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array {
        $sql = "SELECT sr.*, u.email AS user_email FROM support_requests sr JOIN user u ON sr.user_id = u.id WHERE sr.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Update status and admin response of a support request.
     * @param int $id
     * @param string $status
     * @param string $admin_response
     * @return bool
     */
    public function update(int $id, string $status, string $admin_response): bool {
        $sql = 'UPDATE support_requests SET status = :status, admin_response = :response, updated_at = NOW() WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':response' => $admin_response,
            ':id' => $id
        ]);
    }

    /**
     * Get multiple support requests by IDs for export.
     * @param array $ids
     * @return array
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        // Create parameter placeholders
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT sr.*, u.email AS user_email FROM support_requests sr JOIN user u ON sr.user_id = u.id WHERE sr.id IN ($placeholders) ORDER BY sr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        // Execute with ID array
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
