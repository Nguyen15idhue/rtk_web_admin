<?php
// Model lấy nhóm tự động cho user dựa trên voucher đã sử dụng
require_once __DIR__ . '/UserModel.php';
require_once __DIR__ . '/VoucherModel.php';

class UserAutoGroupModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách nhóm tự động theo voucher đã sử dụng
     * Trả về: [user_id => [group1, group2, ...], ...]
     */
    public function getAutoGroupsByVoucher() {
        // Lấy tất cả voucher đã sử dụng của user
        $sql = "SELECT uvu.user_id, v.code as voucher_code
                FROM user_voucher_usage uvu
                JOIN voucher v ON uvu.voucher_id = v.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $userId = $row['user_id'];
            $group = 'Voucher: ' . $row['voucher_code'];
            if (!isset($result[$userId])) $result[$userId] = [];
            if (!in_array($group, $result[$userId])) $result[$userId][] = $group;
        }
        return $result;
    }

    /**
     * Lấy nhóm tự động cho 1 user cụ thể
     */
    public function getGroupsForUser($userId) {
        $sql = "SELECT v.code as voucher_code
                FROM user_voucher_usage uvu
                JOIN voucher v ON uvu.voucher_id = v.id
                WHERE uvu.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $groups = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $groups[] = 'Voucher: ' . $row['voucher_code'];
        }
        return $groups;
    }
}
