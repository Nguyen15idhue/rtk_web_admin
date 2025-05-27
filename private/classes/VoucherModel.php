<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class VoucherModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch paginated vouchers with filters
    public function fetchPaginated(array $filters = [], int $page = 1, int $perPage = 10): array {
        $baseSelect = "SELECT v.id, v.code, v.description, v.voucher_type, v.discount_value, v.max_discount, v.min_order_value, v.quantity, v.limit_usage, v.used_quantity, v.start_date, v.end_date, v.is_active, v.created_at, v.max_sa, l.province as location_name, p.name as package_name";
        $baseFrom = " FROM voucher v LEFT JOIN location l ON v.location_id = l.id LEFT JOIN package p ON v.package_id = p.id";
        $baseWhere = " WHERE 1=1";
        $params = [];
        if (!empty($filters['q'])) {
            $term = '%' . trim($filters['q']) . '%';
            $baseWhere .= " AND (code LIKE ? OR description LIKE ?)";
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['type'])) {
            $baseWhere .= " AND voucher_type = ?";
            $params[] = $filters['type'];
        }
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $baseWhere .= " AND is_active = 1 AND end_date >= NOW()";
            } elseif ($filters['status'] === 'inactive') {
                $baseWhere .= " AND is_active = 0 AND end_date >= NOW()";
            } elseif ($filters['status'] === 'expired') {
                $baseWhere .= " AND end_date < NOW()";
            }
        }
        // count total
        $countSql = "SELECT COUNT(*)" . $baseFrom . $baseWhere;
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $totalPages = $perPage > 0 ? ceil($total / $perPage) : 0;
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        $offset = ($page - 1) * $perPage;

        // fetch data
        $dataSql = $baseSelect . $baseFrom . $baseWhere . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$perPage, $offset]);
        $stmt = $this->db->prepare($dataSql);
        $stmt->execute($dataParams);
        $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'vouchers'     => $vouchers,
            'total_count'  => $total,
            'current_page' => $page,
            'per_page'     => $perPage,
            'total_pages'  => $totalPages
        ];
    }

    // Get voucher by ID
    public function getOne(int $id) {
        $sql = "SELECT v.*, l.province as location_name, p.name as package_name FROM voucher v LEFT JOIN location l ON v.location_id = l.id LEFT JOIN package p ON v.package_id = p.id WHERE v.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new voucher
    public function create(array $data) {
        $sql = "INSERT INTO voucher (code, description, voucher_type, discount_value, max_discount, min_order_value, quantity, limit_usage, start_date, end_date, is_active, max_sa, location_id, package_id, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            $data['code'],
            $data['description'] ?? null,
            $data['voucher_type'],
            $data['discount_value'],
            $data['max_discount'] ?? null,
            $data['min_order_value'] ?? null,
            $data['quantity'] ?? null,
            $data['limit_usage'] ?? null,
            $data['start_date'],
            $data['end_date'],
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $data['max_sa'] ?? null,
            $data['location_id'] ?? null,
            $data['package_id'] ?? null
        ]);
        return $ok ? $this->db->lastInsertId() : false;
    }

    // Update existing voucher
    public function update(int $id, array $data): bool {
        $sql = "UPDATE voucher SET code = ?, description = ?, voucher_type = ?, discount_value = ?, max_discount = ?, min_order_value = ?, quantity = ?, limit_usage = ?, start_date = ?, end_date = ?, is_active = ?, max_sa = ?, location_id = ?, package_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['code'],
            $data['description'] ?? null,
            $data['voucher_type'],
            $data['discount_value'],
            $data['max_discount'] ?? null,
            $data['min_order_value'] ?? null,
            $data['quantity'] ?? null,
            $data['limit_usage'] ?? null,
            $data['start_date'],
            $data['end_date'],
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $data['max_sa'] ?? null,
            $data['location_id'] ?? null,
            $data['package_id'] ?? null,
            $id
        ]);
    }

    // Toggle voucher active status
    public function toggleStatus(int $id, bool $disable): bool {
        $active = $disable ? 0 : 1;
        $sql = "UPDATE voucher SET is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$active, $id]);
    }

    /**
     * Delete a voucher by ID.
     *
     * @param int $id The voucher ID to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM voucher WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Lấy toàn bộ dữ liệu voucher để export Excel
     */
    public function getAllDataForExport(): array {
        $sql = "
            SELECT
                v.id,
                v.code,
                v.description,
                v.voucher_type,
                v.discount_value,
                v.max_discount,
                v.min_order_value,
                v.quantity,
                v.used_quantity,
                v.start_date,
                v.end_date,
                v.is_active,
                v.max_sa,
                l.province as location_name,
                p.name as package_name
            FROM voucher v
            LEFT JOIN location l ON v.location_id = l.id
            LEFT JOIN package p ON v.package_id = p.id
            ORDER BY created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy dữ liệu các voucher có ID nằm trong $ids để export Excel
     *
     * @param array $ids danh sách ID voucher cần export
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "
            SELECT
                v.id,
                v.code,
                v.description,
                v.voucher_type,
                v.discount_value,
                v.max_discount,
                v.min_order_value,
                v.quantity,
                v.used_quantity,
                v.start_date,
                v.end_date,
                v.is_active,
                v.max_sa,
                l.province as location_name,
                p.name as package_name
            FROM voucher v
            LEFT JOIN location l ON v.location_id = l.id
            LEFT JOIN package p ON v.package_id = p.id
            WHERE id IN ({$placeholders})
            ORDER BY created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
