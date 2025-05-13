<?php

class CommissionModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Fetches commission data by a list of IDs for Excel export.
     *
     * @param array $ids An array of commission IDs.
     * @return array An array of commission data.
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT rc.id, u.username AS user_referral, rc.transaction_id, rc.commission_amount, rc.status, rc.created_at 
                FROM referral_commission rc
                LEFT JOIN user u ON rc.referrer_id = u.id
                WHERE rc.id IN ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $_SESSION['last_headers'] = array_keys(reset($data));
        } else {
            // Define default headers if no data is found, matching expected columns
            $_SESSION['last_headers'] = ['id', 'user_referral', 'transaction_id', 'commission_amount', 'status', 'created_at'];
        }
        return $data;
    }

    /**
     * Fetches all commission data, potentially filtered, for Excel export.
     *
     * @param array $filters An array of filters (e.g., ['status' => 'paid', 'search' => 'keyword', 'date_from' => 'Y-m-d', 'date_to' => 'Y-m-d']).
     * @return array An array of commission data.
     */
    public function getAllDataForExport(array $filters = []): array {
        $sql = "SELECT rc.id, u.username AS user_referral, rc.transaction_id, rc.commission_amount, rc.status, rc.created_at 
                FROM referral_commission rc
                LEFT JOIN user u ON rc.referrer_id = u.id";
        
        $whereClauses = [];
        $params = [];

        if (!empty($filters['status'])) {
            $whereClauses[] = "rc.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $whereClauses[] = "(u.username LIKE :search OR rc.transaction_id LIKE :search OR rc.commission_amount LIKE :search)";
            $params[':search'] = $searchTerm;
        }
        if (!empty($filters['date_from'])) {
            $whereClauses[] = "rc.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $whereClauses[] = "rc.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $sql .= " ORDER BY rc.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $_SESSION['last_headers'] = array_keys(reset($data));
        } else {
            // Define default headers if no data is found, matching expected columns
            $_SESSION['last_headers'] = ['id', 'user_referral', 'transaction_id', 'commission_amount', 'status', 'created_at'];
        }
        return $data;
    }

    // You might also need a generic getAll method if not using getAllDataForExport exclusively
    public function getAll(array $filters = []): array {
        // This could be similar to getAllDataForExport or a more generic version
        return $this->getAllDataForExport($filters);
    }
}
