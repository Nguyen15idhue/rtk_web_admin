<?php
require_once BASE_PATH . '/config/constants.php'; // Ensure constants are loaded
require_once BASE_PATH . '/classes/RtkApiClient.php'; // Include the RtkApiClient class

class StationModel {

    public function __construct() {
        // Constructor can be left empty or used for other initializations
    }

    /**
     * Get all stations with their manager name.
     *
     * @return array An array of station objects or associative arrays.
     */
    public function getAllStations(): array {
        $sql = "SELECT 
                    s.*, 
                    m.name as manager_name 
                FROM station s -- Changed from stations to station
                LEFT JOIN manager m ON s.manager_id = m.id
                ORDER BY s.station_name ASC"; // Order by station_name as per new schema
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in StationModel::getAllStations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single station by its ID, including manager name.
     *
     * @param mixed $id The ID of the station.
     * @return array|null The station data as an associative array, or null if not found.
     */
    public function getStationById($id): ?array {
        $sql = "SELECT 
                    s.*, 
                    m.name as manager_name 
                FROM station s -- Changed from stations to station
                LEFT JOIN manager m ON s.manager_id = m.id
                WHERE s.id = :id";
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR); // id is VARCHAR
            $stmt->execute();
            $station = $stmt->fetch(PDO::FETCH_ASSOC);
            return $station ?: null;
        } catch (PDOException $e) {
            error_log("Error in StationModel::getStationById (ID: {$id}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update station's manager and mountpoint information.
     *
     * @param string $station_id The ID of the station to update (VARCHAR).
     * @param string|null $manager_id The ID of the new manager (VARCHAR).
     * @param string|null $mountpoint_id_from_api The API ID of the selected mountpoint (VARCHAR for station.mountpoint_id).
     * @return bool True on success, false on failure.
     */
    public function updateStation($station_id, $manager_id, $mountpoint_id_from_api): bool {
        // SQL query updated to match the 'station' table schema provided by user.
        // Only manager_id and mountpoint_id are updated.
        // No updated_at, mountpoint_name, mountpoint_master_station_names.
        $sql = "UPDATE station SET 
                    manager_id = :manager_id, 
                    mountpoint_id = :mountpoint_id_from_api
                WHERE id = :station_id";
        
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);

            // Bind parameters according to new schema (VARCHAR types for IDs)
            $stmt->bindParam(':station_id', $station_id, PDO::PARAM_STR);
            $stmt->bindParam(':manager_id', $manager_id, $manager_id === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':mountpoint_id_from_api', $mountpoint_id_from_api, $mountpoint_id_from_api === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in StationModel::updateStation (ID: {$station_id}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch mountpoints from RTK API using the RtkApiClient.
     *
     * @param int $page Page number.
     * @param int $size Number of records per page.
     * @return array The API response data (records array) or an empty array on failure.
     */
    public function fetchMountpointsFromAPI(int $page = 1, int $size = 100): array {
        try {
            $client = new RtkApiClient(); // Sử dụng default timeout thay vì custom
            $params = ['page' => $page, 'size' => $size];
            
            $response = $client->request('GET', '/openapi/broadcast/mounts', $params);
            
            if ($response['success'] && isset($response['data']['records'])) {
                // Log thông tin để debug
                $total = $response['data']['total'] ?? 0;
                $recordCount = count($response['data']['records']);
                error_log("StationModel::fetchMountpointsFromAPI - Page {$page}: Got {$recordCount} records out of {$total} total");
                
                return $response['data']['records'];
            } else {
                $errorMsg = $response['error'] ?? 'Unknown API error';
                error_log("StationModel::fetchMountpointsFromAPI - API Error: {$errorMsg}");
                if (isset($response['data'])) {
                    error_log("StationModel::fetchMountpointsFromAPI - Response Data: " . json_encode($response['data']));
                }
                return [];
            }
        } catch (Exception $e) {
            error_log("StationModel::fetchMountpointsFromAPI - Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch ALL mountpoints from RTK API (handles pagination automatically).
     *
     * @return array All mountpoints from all pages
     */
    public function fetchAllMountpointsFromAPI(): array {
        $allMountpoints = [];
        $page = 1;
        $size = 100;
        $total = null;
        
        do {
            try {
                $client = new RtkApiClient(); // Sử dụng default timeout
                $params = ['page' => $page, 'size' => $size];
                
                $response = $client->request('GET', '/openapi/broadcast/mounts', $params);
                
                if ($response['success'] && isset($response['data']['records'])) {
                    $records = $response['data']['records'];
                    $total = $response['data']['total'] ?? 0;
                    
                    if (empty($records)) {
                        break; // No more data
                    }
                    
                    $allMountpoints = array_merge($allMountpoints, $records);
                    
                    // Kiểm tra xem đã lấy đủ chưa
                    if (count($allMountpoints) >= $total) {
                        error_log("StationModel::fetchAllMountpointsFromAPI - Completed: Got all {$total} records");
                        break;
                    }
                    
                    $page++;
                    
                    // Safety check để tránh vòng lặp vô hạn
                    if ($page > 50) {
                        error_log("StationModel::fetchAllMountpointsFromAPI - Safety break at page 50");
                        break;
                    }
                    
                } else {
                    $errorMsg = $response['error'] ?? 'Unknown API error';
                    error_log("StationModel::fetchAllMountpointsFromAPI - API Error on page {$page}: {$errorMsg}");
                    break;
                }
                
            } catch (Exception $e) {
                error_log("StationModel::fetchAllMountpointsFromAPI - Exception on page {$page}: " . $e->getMessage());
                break;
            }
            
        } while (true);
        
        $finalCount = count($allMountpoints);
        error_log("StationModel::fetchAllMountpointsFromAPI - Final result: {$finalCount} mountpoints" . ($total ? " (expected: {$total})" : ""));
        
        return $allMountpoints;
    }

    /**
     * Export all data.
     *
     * @return array An array of all station data.
     */
    public function getAllDataForExport(): array {
        return $this->getAllStations();
    }

    /**
     * Export data by a list of IDs.
     *
     * @param array $ids An array of station IDs.
     * @return array An array of station data corresponding to the provided IDs.
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        // batch fetch stations by IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT s.*, m.name as manager_name
                FROM station s
                LEFT JOIN manager m ON s.manager_id = m.id
                WHERE s.id IN ($placeholders)
                ORDER BY s.station_name ASC";
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($ids);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in StationModel::getDataByIdsForExport: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Debug method to get detailed API response.
     *
     * @return array Full API response for debugging
     */
    public function debugMountpointsAPI(): array {
        try {
            $client = new RtkApiClient();
            $params = ['page' => 1, 'size' => 100];
            
            $response = $client->request('GET', '/openapi/broadcast/mounts', $params);
            
            error_log("StationModel::debugMountpointsAPI - Full response: " . json_encode($response, JSON_PRETTY_PRINT));
            
            return $response;
            
        } catch (Exception $e) {
            error_log("StationModel::debugMountpointsAPI - Exception: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
?>
