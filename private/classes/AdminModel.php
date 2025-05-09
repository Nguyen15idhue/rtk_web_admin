<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class AdminModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function getAll($search = '') {
        $sql = "SELECT id, name, admin_username AS username, role, created_at
                FROM admin
                WHERE name LIKE ? OR admin_username LIKE ?
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(int $id) {
        $sql = "SELECT id, name, admin_username AS username, role, created_at
                FROM admin
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername(string $username) {
        $sql = "SELECT * FROM admin WHERE admin_username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO admin (name, admin_username, admin_password, role, created_at)
                VALUES (:name, :username, :pwd, :role, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name'     => $data['name'],
            ':username' => $data['username'],
            ':pwd'      => $hashed,
            ':role'     => $data['role']
        ]);
    }

    public function update(int $id, array $data) {
        $parts = [];
        $params = [':id' => $id];
        if (!empty($data['name'])) {
            $parts[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (!empty($data['role'])) {
            $parts[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        if (!empty($data['password'])) {
            $parts[] = "admin_password = :pwd";
            $params[':pwd'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (empty($parts)) {
            return false;
        }
        $sql = "UPDATE admin SET " . implode(', ', $parts) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id) {
        $sql = "DELETE FROM admin WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getProfileById($admin_id) {
        if (!$this->conn) {
            error_log("Database connection failed in AdminModel::getProfileById");
            return false;
        }
        try {
            $stmt = $this->conn->prepare("SELECT name, admin_username, role FROM admin WHERE id = :id");
            $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching profile in AdminModel: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($admin_id, $name) {
        if (!$this->conn) {
            error_log("Database connection failed in AdminModel::updateProfile");
            return false;
        }
        try {
            $stmt = $this->conn->prepare("UPDATE admin SET name = :name, updated_at = NOW() WHERE id = :id");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating admin profile in AdminModel: " . $e->getMessage());
            return false;
        }
    }

    public function getPasswordHashById($admin_id) {
        if (!$this->conn) {
            error_log("Database connection failed in AdminModel::getPasswordHashById");
            return false;
        }
        try {
            $stmt = $this->conn->prepare("SELECT admin_password FROM admin WHERE id = :id");
            $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            return $admin ? $admin['admin_password'] : null;
        } catch (PDOException $e) {
            error_log("Error fetching password hash in AdminModel: " . $e->getMessage());
            return null;
        }
    }

    public function updatePassword($admin_id, $new_password_hash) {
        if (!$this->conn) {
            error_log("Database connection failed in AdminModel::updatePassword");
            return false;
        }
        try {
            $updateStmt = $this->conn->prepare("UPDATE admin SET admin_password = :new_password, updated_at = NOW() WHERE id = :id");
            $updateStmt->bindParam(':new_password', $new_password_hash, PDO::PARAM_STR);
            $updateStmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
            return $updateStmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating admin password in AdminModel: " . $e->getMessage());
            return false;
        }
    }

    public function __destruct() {
        if ($this->db) {
            // $this->db->close(); // Database class should handle its own lifecycle or be closed at script end
        }
    }
}
?>
