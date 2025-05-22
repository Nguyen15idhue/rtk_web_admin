<?php
require_once __DIR__ . '/../config/constants.php'; // Ensure constants are loaded

class StationModel {
    private $api_base_url = 'http://203.171.25.138:8090/openapi/broadcast/mounts';

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
     * Fetch mountpoints from the external API.
     *
     * @param int $page Page number.
     * @param int $size Number of records per page.
     * @return array The API response data (records array) or an empty array on failure.
     */
    public function fetchMountpointsFromAPI(int $page = 1, int $size = 100): array {
        $queryString = "page={$page}&size={$size}";
        $url = $this->api_base_url . "?{$queryString}";
        
        $nonce = bin2hex(random_bytes(16));
        $timestamp = (string)(round(microtime(true) * 1000));
        $accessKey = defined('API_ACCESS_KEY') ? API_ACCESS_KEY : '';
        $secretKey = defined('API_SECRET_KEY') ? API_SECRET_KEY : '';
        $signMethod = 'HmacSHA256';

        $headersToSign = [
            'X-Nonce' => $nonce,
            'X-Access-Key' => $accessKey,
            'X-Sign-Method' => $signMethod,
            'X-Timestamp' => $timestamp
        ];

        $signPath = '/openapi/broadcast/mounts'; // Path part of the URL

        // String to sign: METHOD URI Headers (URI without query string for signing)
        $stringToSign = "GET {$signPath} "; // Note the space at the end. Query string is NOT included here.
        
        $headerSignParts = [];
        foreach ($headersToSign as $key => $value) {
            $headerSignParts[strtolower($key)] = $value;
        }
        ksort($headerSignParts);
        $headerComponent = "";
        foreach ($headerSignParts as $key => $value) {
            $headerComponent .= "{$key}={$value}&";
        }
        $headerComponent = rtrim($headerComponent, '&');
        $stringToSign .= $headerComponent;

        $sign = hash_hmac('sha256', $stringToSign, $secretKey);
        
        $curlHeaders = [];
        foreach ($headersToSign as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }
        $curlHeaders[] = "Sign: $sign";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($http_code == 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['code']) && $data['code'] === 'SUCCESS' && isset($data['data']['records'])) {
                return $data['data']['records'];
            } else {
                error_log("StationModel::fetchMountpointsFromAPI - API Error: " . ($data['msg'] ?? 'Unknown API error or malformed response') . " | Response: " . $response);
                return [];
            }
        } else {
            error_log("StationModel::fetchMountpointsFromAPI - cURL Error: {$curl_error} (HTTP Code: {$http_code}) for URL: {$url}");
            return [];
        }
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
}
?>
