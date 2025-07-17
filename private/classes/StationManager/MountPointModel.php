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

    /**
     * Auto-update location_id for mountpoints based on first 3 characters of masterStationNames
     * by matching with province_code in location table.
     *
     * @param array $mountpointsFromAPI Array of mountpoints from API with masterStationNames
     * @return array Result with success status, updated count, and details
     */
    public function autoUpdateLocationsByMasterStationNames(array $mountpointsFromAPI): array {
        try {
            // Get all locations with their province codes
            $sql = "SELECT id, province, province_code FROM location WHERE status = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Create a map of province_code => location_id
            $provinceCodeMap = [];
            foreach ($locations as $location) {
                $provinceCodeMap[strtoupper($location['province_code'])] = $location['id'];
            }
            
            $updatedCount = 0;
            $updatedDetails = [];
            $errors = [];
            
            foreach ($mountpointsFromAPI as $mountpoint) {
                $mountpointId = $mountpoint['id'] ?? '';
                $masterStationNames = $mountpoint['masterStationNames'] ?? [];
                
                if (empty($mountpointId) || empty($masterStationNames)) {
                    continue;
                }
                
                // Process each masterStationName to find matching province code
                foreach ($masterStationNames as $stationName) {
                    if (strlen($stationName) >= 3) {
                        $first3Chars = strtoupper(substr($stationName, 0, 3));
                        
                        if (isset($provinceCodeMap[$first3Chars])) {
                            $locationId = $provinceCodeMap[$first3Chars];
                            
                            // Update the mountpoint location
                            if ($this->updateMountPointLocation($mountpointId, $locationId)) {
                                $updatedCount++;
                                $updatedDetails[] = [
                                    'mountpoint_id' => $mountpointId,
                                    'station_name' => $stationName,
                                    'matched_code' => $first3Chars,
                                    'location_id' => $locationId,
                                    'province' => array_search($locationId, $provinceCodeMap) 
                                ];
                                break; // Exit loop after first match
                            } else {
                                $errors[] = "Failed to update mountpoint {$mountpointId}";
                            }
                        }
                    }
                }
            }
            
            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'total_processed' => count($mountpointsFromAPI),
                'updated_details' => $updatedDetails,
                'errors' => $errors
            ];
            
        } catch (PDOException $e) {
            error_log("Error in MountPointModel::autoUpdateLocationsByMasterStationNames: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'updated_count' => 0,
                'total_processed' => 0,
                'updated_details' => [],
                'errors' => []
            ];
        }
    }
}
