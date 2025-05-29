<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\classes\PackageModel.php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class PackageModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllPackages() {
        try {
            $stmt = $this->db->query("SELECT id, name FROM package ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in PackageModel::getAllPackages: " . $e->getMessage());
            return [];
        }
    }
}
?>
