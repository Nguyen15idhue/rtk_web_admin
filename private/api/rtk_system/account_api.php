<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
require_once __DIR__ . '/../../classes/RtkApiClient.php'; // Include the RtkApiClient class

// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', LOGS_PATH . '/error.log');

/**
 * Tạo tài khoản RTK mới qua API.
 * 
 * @param array $accountData Dữ liệu tài khoản cần tạo
 * @return array Response dạng ['success' => bool, 'data' => array|null, 'error' => string|null]
 */
function createRtkAccount(array $accountData): array {
    try {
        // Validate required fields
        $requiredFields = ['name', 'userPwd', 'startTime', 'endTime'];
        foreach ($requiredFields as $field) {
            if (empty($accountData[$field])) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => "Missing required field: $field"
                ];
            }
        }

        // Set default values for optional fields
        $accountData = array_merge([
            'enabled' => 1,
            'numOnline' => 1,
            'customerBizType' => 1,
            'customerCompany' => [],
            'casterIds' => [],
            'regionIds' => [],
            'mountIds' => []
        ], $accountData);

        $client = new RtkApiClient();
        return $client->request('POST', '/openapi/broadcast/users', $accountData);
    } catch (Exception $e) {
        error_log(
            "createRtkAccount Exception: " . $e->getMessage() .
            "\nPayload: " . json_encode($accountData) .
            "\nTrace:\n" . $e->getTraceAsString()
        );
        return [
            'success' => false,
            'data' => null,
            'error' => "Exception: " . $e->getMessage()
        ];
    }
}

/**
 * Lấy danh sách mount point ID dạng số dựa trên location ID
 * 
 * @param int $locationId ID của địa điểm (tỉnh/thành phố)
 * @return array Danh sách các mount point ID dạng số
 */
function getMountPointsByLocationId(int $locationId): array {
    try {
        require_once BASE_PATH . '/classes/Database.php';
        $database = Database::getInstance();
        $pdo = $database->getConnection();
        if (!$pdo) {
            error_log("Error connecting for mount points (loc {$locationId}): Failed via Database class");
            // fallback defaults
            switch ($locationId) {
                case 63: return [44,45,46,47,48,49,64];
                case 24: return [1,2,3];
                default:   return [40 + $locationId % 10];
            }
        }
        
        // Tìm mount point IDs và chuyển thành số (API yêu cầu giá trị số)
        $mountIds = [];
        
        try {
            // Phương pháp 1: Sử dụng REGEXP_REPLACE để lấy phần số từ ID
            $stmt = $pdo->prepare("SELECT CAST(REGEXP_REPLACE(id, '[^0-9]', '') AS UNSIGNED) as numeric_id FROM mount_point WHERE location_id = :location_id");
            $stmt->bindParam(':location_id', $locationId, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['numeric_id'])) {
                    $mountIds[] = (int)$row['numeric_id']; // Đảm bảo là số nguyên
                }
            }
        } catch (PDOException $e) {
            // Phương pháp 2: Lấy ID nguyên gốc và xử lý bằng regex
            $stmt = $pdo->prepare("SELECT id FROM mount_point WHERE location_id = :location_id");
            $stmt->bindParam(':location_id', $locationId, PDO::PARAM_INT);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Tách phần số từ ID hoặc tạo một số thay thế
                preg_match('/(\d+)/', $row['id'], $matches);
                if (!empty($matches[1])) {
                    $mountIds[] = (int)$matches[1]; // Chuyển thành số nguyên
                } else {
                    // Sử dụng hash của ID làm giá trị số nếu không tìm thấy số
                    $mountIds[] = abs(crc32($row['id'])) % 1000 + 1000; // Tạo một số nguyên dương
                }
            }
        }
        
        // Nếu không tìm thấy mount points, sử dụng ID mặc định dựa vào location
        if (empty($mountIds)) {
            // Default mount point IDs based on location
            switch ($locationId) {
                case 63: // Yên Bái
                    $mountIds = [44, 45, 46, 47, 48, 49, 64];
                    break;
                case 24: // Hà Nội
                    $mountIds = [1, 2, 3];
                    break;
                default:
                    $mountIds = [40 + $locationId % 10]; // Tạo ID hợp lý dựa trên locationId
            }
            error_log("Using default mount points for location ID: $locationId - " . json_encode($mountIds));
        }
        
        return $mountIds;
    } catch (PDOException $e) {
        error_log("Error fetching mount points for location $locationId: " . $e->getMessage());
        // fallback defaults
        switch ($locationId) {
            case 63: return [44,45,46,47,48,49,64];
            case 24: return [1,2,3];
            default:   return [40 + $locationId % 10];
        }
    }
}

/**
 * Cập nhật tài khoản RTK qua API.
 * 
 * @param array $accountData Dữ liệu tài khoản cần cập nhật
 * @return array Response dạng ['success' => bool, 'data' => array|null, 'error' => string|null]
 */
function updateRtkAccount(array $accountData): array {
    try {
        // Extract and validate ID
        $id = $accountData['id'] ?? '';
        if (empty($id)) {
            return ['success' => false, 'data' => null, 'error' => 'Missing required field: id'];
        }
        unset($accountData['id']);

        // Validate required fields
        $required = ['name', 'userPwd', 'startTime', 'endTime'];
        foreach ($required as $f) {
            if (empty($accountData[$f])) {
                return ['success' => false, 'data' => null, 'error' => "Missing required field: $f"];
            }
        }

        // Set default optional values
        $accountData = array_merge([
            'enabled'         => 1,
            'numOnline'       => 1,
            'customerBizType' => 1,
            'customerCompany' => '',
            'casterIds'       => [],
            'regionIds'       => [],
            'mountIds'        => []
        ], $accountData);

        $client = new RtkApiClient();
        return $client->request('PUT', "/openapi/broadcast/users/{$id}", $accountData);
    } catch (Exception $e) {
        error_log(
            "updateRtkAccount Exception for ID {$id}: " . $e->getMessage() .
            "\nPayload: " . json_encode($accountData) .
            "\nTrace:\n" . $e->getTraceAsString()
        );
        return ['success' => false, 'data' => null, 'error' => "Exception: " . $e->getMessage()];
    }
}

/**
 * Xóa tài khoản RTK qua API.
 *
 * @param array $ids Mảng ID tài khoản cần xóa
 * @return array ['success'=>bool,'data'=>array|null,'error'=>string|null]
 */
function deleteRtkAccount(array $ids): array {
    $client = new RtkApiClient();
    return $client->request('POST', '/openapi/broadcast/users/delete', ['ids' => $ids]);
}

/**
 * GET list of stations (id→identificationName, lat, lng).
 */
function fetchAllStations(): array {
    $client = new RtkApiClient();
    $resp = $client->request('GET', '/openapi/stream/stations?page=1&size=100&count=true');
    error_log("RTK API response: " . json_encode($resp));
    $records = $resp['data']['records'] ?? [];
    $out = [];
    foreach ($records as $r) {
        $out[(int)$r['id']] = [
            'stationName'         => $r['stationName']         ?? '',
            'identificationName'  => $r['identificationName']  ?? '',
            'lat'                 => $r['lat']                 ?? null,
            'lng'                 => $r['lng']                 ?? null,
        ];
    }
    error_log("Fetched " . count($out) . " stations from RTK API");
    return $out;
}

/**
 * POST dynamic-info with array of ids.
 */
function fetchStationDynamicInfo(array $ids): array {
    $client = new RtkApiClient();
    $resp = $client->request('POST', '/openapi/stream/stations/dynamic-info', ['ids' => $ids]);
    return $resp['data'] ?? [];
}

/**
 * Fetch stations + dynamic info, then UPDATE `station` table.
 */
function fetchAndUpdateStations(): void {
    // Initialize MySQLi connection using defined database constants
    require_once BASE_PATH . '/config/database.php';
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        error_log("DB connect failed: " . $conn->connect_error);
        return;
    }
    // Set charset to UTF-8
    if (!$conn->set_charset("utf8mb4")) {
        error_log(sprintf("Error loading character set utf8mb4: %s\n", $conn->error));
    }

    // 1. Lấy danh sách station từ API
    $map = fetchAllStations();
    if (empty($map)) return;
    $dynList = fetchStationDynamicInfo(array_keys($map));
    // chuyển thành map theo stationId để tiện truy cập
    $dyn = [];
    foreach ($dynList as $s) {
        $dyn[$s['stationId']] = $s;
    }

    // 2. Lấy danh sách ID đang có trong DB
    $existingIds = [];
    $res = $conn->query("SELECT id FROM station");
    while ($row = $res->fetch_assoc()) {
        $existingIds[] = (int)$row['id'];
    }

    $apiIds = array_map('intval', array_keys($map));

    // 3. Xoá trạm không còn trong API
    $toDelete = array_diff($existingIds, $apiIds);
    if (!empty($toDelete)) {
        $ids = implode(',', $toDelete);
        // Soft-delete: gán status = -1
        $conn->query("UPDATE station SET status = -1 WHERE id IN ($ids)");
    }

    // 4. Thêm mới trạm API chưa có trong DB
    $toInsert = array_diff($apiIds, $existingIds);
    if (!empty($toInsert)) {
        $stmtI = $conn->prepare("
            INSERT INTO station
                (id, station_name, identificationName, lat, `long`, status, mountpoint_id)
            VALUES (?,   ?,            ?,                 ?,    ?,      ?,      NULL)
        ");
        foreach ($toInsert as $id) {
            $stationName = $map[$id]['stationName'] ?? '';
            $identName   = $map[$id]['identificationName'] ?? '';
            $lat         = $map[$id]['lat']                ?? 0.0;
            $lng         = $map[$id]['lng']                ?? 0.0;
            $status      = $dyn[$id]['connectStatus']      ?? 0;
            $stmtI->bind_param('sssddi', $id, $stationName, $identName, $lat, $lng, $status);
            $stmtI->execute();
        }
        $stmtI->close();
    }

    // 5. Cập nhật station_name, identificationName, lat, long, status cho trạm tồn tại
    $stmtU = $conn->prepare("
        UPDATE station
           SET station_name       = ?,
               identificationName = ?,
               lat                = ?,
               `long`             = ?,
               status             = ?
         WHERE id = ?
    ");
    foreach ($apiIds as $id) {
        $stationName = $map[$id]['stationName'];
        $identName   = $map[$id]['identificationName'];
        $lat         = $map[$id]['lat']            ?? 0.0;
        $lng         = $map[$id]['lng']            ?? 0.0;
        $status      = $dyn[$id]['connectStatus']  ?? 0;
        $stmtU->bind_param('ssddis', $stationName, $identName, $lat, $lng, $status, $id);
        $stmtU->execute();
    }
    $stmtU->close();
    $conn->close();
}

?>