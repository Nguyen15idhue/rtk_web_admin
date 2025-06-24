<?php
require_once BASE_PATH . '/config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

class MountPointModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all mount points with their associated location name.
     *
     * @return array
     */
    public function getAllMountPoints(): array {
        $sql = "SELECT mp.*, l.province AS location_name
                FROM mount_point mp
                LEFT JOIN location l ON mp.location_id = l.id
                ORDER BY l.province ASC, mp.mountpoint ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in MountPointModel::getAllMountPoints: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update the location assignment of a mount point.
     *
     * @param string $mountpointId
     * @param int|null $locationId
     * @return bool
     */
    public function updateMountPointLocation(string $mountpointId, $locationId): bool {
        $sql = "UPDATE mount_point SET location_id = :location_id WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $mountpointId, PDO::PARAM_STR);
            if ($locationId === null || $locationId === '') {
                $stmt->bindValue(':location_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':location_id', $locationId, PDO::PARAM_INT);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in MountPointModel::updateMountPointLocation (ID: {$mountpointId}): " . $e->getMessage());
            return false;
        }
    }
}
