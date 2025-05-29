<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\classes\LocationModel.php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class LocationModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllLocations() {
        try {
            $stmt = $this->db->query("SELECT id, province FROM location ORDER BY province ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in LocationModel::getAllLocations: " . $e->getMessage());
            return [];
        }
    }
}
?>
