<?php

class WithdrawalRequestModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Fetches withdrawal request data by a list of IDs for Excel export.
     *
     * @param array $ids An array of withdrawal request IDs.
     * @return array An array of withdrawal request data.
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT wr.id, u.username AS user_request, wr.amount, wr.bank_name, wr.account_number, wr.account_holder, wr.status, wr.notes, wr.created_at AS requested_at, wr.updated_at AS processed_at 
                FROM withdrawal_request wr
                LEFT JOIN user u ON wr.user_id = u.id
                WHERE wr.id IN ({$placeholders})";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $_SESSION['last_headers'] = array_keys(reset($data));
        } else {
            $_SESSION['last_headers'] = ['id', 'user_request', 'amount', 'bank_name', 'account_number', 'account_holder', 'status', 'notes', 'requested_at', 'processed_at'];
        }
        return $data;
    }

    /**
     * Fetches all withdrawal request data, potentially filtered, for Excel export.
     *
     * @param array $filters An array of filters (e.g., ['status' => 'pending', 'search' => 'keyword', 'date_from' => 'Y-m-d', 'date_to' => 'Y-m-d']).
     * @return array An array of withdrawal request data.
     */
    public function getAllDataForExport(array $filters = []): array {
        $sql = "SELECT wr.id, u.username AS user_request, wr.amount, wr.bank_name, wr.account_number, wr.account_holder, wr.status, wr.notes, wr.created_at AS requested_at, wr.updated_at AS processed_at 
                FROM withdrawal_request wr
                LEFT JOIN user u ON wr.user_id = u.id";
        
        $whereClauses = [];
        $params = [];

        if (!empty($filters['status'])) {
            $whereClauses[] = "wr.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $whereClauses[] = "(u.username LIKE :search OR wr.amount LIKE :search OR wr.bank_name LIKE :search OR wr.account_holder LIKE :search OR wr.notes LIKE :search)";
            $params[':search'] = $searchTerm;
        }
        if (!empty($filters['date_from'])) {
            $whereClauses[] = "wr.created_at >= :date_from"; // Use created_at for requested_at
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $whereClauses[] = "wr.created_at <= :date_to"; // Use created_at for requested_at
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY wr.created_at DESC"; // Order by created_at

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $_SESSION['last_headers'] = array_keys(reset($data));
        } else {
            $_SESSION['last_headers'] = ['id', 'user_request', 'amount', 'bank_name', 'account_number', 'account_holder', 'status', 'notes', 'requested_at', 'processed_at'];
        }
        return $data;
    }
    
    // You might also need a generic getAll method if not using getAllDataForExport exclusively
    public function getAll(array $filters = []): array {
        // This could be similar to getAllDataForExport or a more generic version
        return $this->getAllDataForExport($filters);
    }
}
