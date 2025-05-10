<?php

class ManagerModel {
    // Không cần thuộc tính $db nữa vì chúng ta sẽ lấy instance mỗi khi cần
    // private $db; 

    public function __construct() {
        // Constructor có thể để trống hoặc dùng cho việc khác nếu cần
        // Việc khởi tạo Database instance sẽ được thực hiện trong từng phương thức
    }

    /**
     * Find manager ID by name.
     *
     * @param string $name The name of the manager.
     * @return string|null The manager ID if found, otherwise null.
     */
    public function findManagerIdByName(string $name): ?string {
        $sql = "SELECT id FROM manager WHERE name = :name LIMIT 1";
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['id'] : null;
        } catch (PDOException $e) {
            error_log("Error in ManagerModel::findManagerIdByName: " . $e->getMessage());
            // Có thể throw exception hoặc trả về null/false tùy theo cách xử lý lỗi chung của ứng dụng
            return null;
        }
    }

    /**
     * Get all managers.
     *
     * @return array An array of all managers.
     */
    public function getAllManagers(): array {
        $sql = "SELECT id, name FROM manager ORDER BY name ASC";
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in ManagerModel::getAllManagers: " . $e->getMessage());
            return [];
        }
    }
}
?>