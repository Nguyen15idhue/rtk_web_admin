<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class UserModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy chi tiết user theo ID
    public function getOne(int $id) {
        $sql = "SELECT id, username, email, phone, is_company, company_name, tax_code,
                       created_at, updated_at, deleted_at
                FROM user WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Phân trang & lọc user
    public function fetchPaginated(array $filters = [], int $page = 1, int $perPage = 10): array {
        // ...build query giống fetch_paginated_users...
        $baseSelect = "SELECT id, username, email, phone, is_company, company_name, tax_code,
                              created_at, updated_at, deleted_at";
        $baseFrom  = " FROM user";
        $baseWhere = " WHERE 1=1";
        $params = [];
        if (!empty($filters['search'])) {
            $term = '%'.trim($filters['search']).'%';
            $baseWhere .= " AND (email LIKE :s OR username LIKE :s OR company_name LIKE :s)";
            $params[':s'] = $term;
        }
        if (!empty($filters['status'])) {
            $baseWhere .= $filters['status']==='inactive'
                        ? " AND deleted_at IS NOT NULL"
                        : " AND deleted_at IS NULL";
        }
        // đếm tổng
        $countSql = "SELECT COUNT(*)".$baseFrom.$baseWhere;
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $totalPages = $perPage>0 ? ceil($total/$perPage) : 0;
        $page = max(1, min($page, $totalPages>0 ? $totalPages : 1));
        $offset = ($page-1)*$perPage;

        // lấy dữ liệu
        $dataSql = $baseSelect.$baseFrom.$baseWhere." ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";
        $params[':limit']  = $perPage;
        $params[':offset'] = $offset;
        $stmt = $this->db->prepare($dataSql);
        foreach ($params as $k=>$v) {
            $stmt->bindValue($k, $v, is_int($v)?PDO::PARAM_INT:PDO::PARAM_STR);
        }
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'users'=>$users, 'total_count'=>$total,
            'current_page'=>$page, 'per_page'=>$perPage,
            'total_pages'=>$totalPages
        ];
    }

    // Tạo user mới
    public function create(array $data) {
        $sql = "INSERT INTO user
                (username,email,password,phone,is_company,company_name,tax_code,created_at)
                VALUES(?,?,?,?,?,?,?,NOW())";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            $data['username'], $data['email'], $data['password'],
            $data['phone'] ?? null, (int)$data['is_company'],
            $data['company_name'] ?? null, $data['tax_code'] ?? null
        ]);
        return $ok ? $this->db->lastInsertId() : false;
    }

    // Cập nhật thông tin user
    public function update(int $id, array $data): bool {
        $sql = "UPDATE user SET
                    username=:u, email=:e, phone=:p,
                    is_company=:c, company_name=:n, tax_code=:t,
                    updated_at=NOW()
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u',$data['username']);
        $stmt->bindParam(':e',$data['email']);
        if (empty($data['phone'])) {
            $stmt->bindValue(':p', null, PDO::PARAM_NULL);
        } else $stmt->bindParam(':p',$data['phone']);
        $stmt->bindParam(':c',$data['is_company'],PDO::PARAM_INT);
        $stmt->bindParam(':n',$data['company_name']);
        $stmt->bindParam(':t',$data['tax_code']);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Bật/tắt user
    public function toggleStatus(int $id, bool $disable): bool {
        $deletedAt = $disable ? date('Y-m-d H:i:s') : null;
        $sql = "UPDATE user SET deleted_at=:d, updated_at=NOW() WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':d',$deletedAt,
            $deletedAt===null?PDO::PARAM_NULL:PDO::PARAM_STR);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra duplicate email/phone (optionally exclude 1 ID)
    public function isDuplicate(string $email, ?string $phone, int $excludeId = null): bool {
        $sql = "SELECT id FROM user 
                WHERE (email = :email OR (phone IS NOT NULL AND phone = :phone))";
        if ($excludeId) $sql .= " AND id != :ex";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':phone',$phone);
        if ($excludeId) $stmt->bindParam(':ex',$excludeId,PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    // Tạo user + tạo user_settings trong 1 transaction
    public function createWithSettings(array $data): int {
        $this->db->beginTransaction();
        $id = $this->create($data);
        if (!$id) {
            $this->db->rollBack();
            throw new Exception('Không thể thêm user.');
        }
        $sql = "INSERT INTO user_settings(user_id,created_at) VALUES(?,NOW())";
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute([$id])) {
            $this->db->rollBack();
            throw new Exception('Không thể tạo setting cho user.');
        }
        $this->db->commit();
        return $id;
    }

    // Update kèm kiểm tra duplicate và transaction
    public function updateWithDuplicateCheck(int $id, array $data): bool {
        $this->db->beginTransaction();
        if ($this->isDuplicate($data['email'],$data['phone'],$id)) {
            $this->db->rollBack();
            throw new Exception('Email hoặc số điện thoại đã tồn tại.');
        }
        if (!$this->update($id,$data)) {
            $this->db->rollBack();
            throw new Exception('Không thể cập nhật user.');
        }
        $this->db->commit();
        return true;
    }

    public function __destruct() {
        Database::getInstance()->close();
    }
}
