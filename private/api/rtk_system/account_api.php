<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class
require_once __DIR__ . '/../../classes/RtkApiClient.php'; // Include the RtkApiClient class

// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', LOGS_PATH . '/error.log'); // Direct PHP error_log to application log

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

        // Remove userId if it's null to avoid RTK API errors
        if (isset($accountData['userId']) && $accountData['userId'] === null) {
            unset($accountData['userId']);
        }

        // Debug logging - log payload before sending to RTK API
        error_log("[createRtkAccount] Final payload for RTK API: " . json_encode($accountData, JSON_PRETTY_PRINT));

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
    // Load database connection
    require_once BASE_PATH . '/classes/Database.php';
    $pdo = Database::getInstance()->getConnection();
    $mountIds = [];
    if ($pdo) {
        // Fetch all mount_point IDs for given location
        $stmt = $pdo->prepare("SELECT id FROM mount_point WHERE location_id = :location_id");
        $stmt->bindParam(':location_id', $locationId, PDO::PARAM_INT);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mountIds[] = (int)$row['id'];
        }
    }
    return $mountIds;
}

/**
 * Cập nhật tài khoản RTK qua API.
 * 
 * @param array $accountData Dữ liệu tài khoản cần cập nhật
 * @return array Response dạng ['success' => bool, 'data' => array|null, 'error' => string|null]
 */
function updateRtkAccount(array $accountData): array {
    $id = ''; // Initialize to avoid undefined variable in catch block
    try {
        // Extract and validate ID
        $id = $accountData['id'] ?? '';
        if (empty($id)) {
            error_log("[updateRtkAccount] Missing required field: id. Payload: " . json_encode($accountData));
            return ['success' => false, 'data' => null, 'error' => 'Missing required field: id'];
        }
        unset($accountData['id']);

        // Remove userId if it's null to avoid RTK API errors - DO THIS FIRST
        if (isset($accountData['userId']) && $accountData['userId'] === null) {
            unset($accountData['userId']);
        }

        // Validate required fields
        $required = ['name', 'userPwd', 'startTime', 'endTime'];
        foreach ($required as $f) {
            if (!isset($accountData[$f]) || $accountData[$f] === '' || $accountData[$f] === null) {
                error_log("[updateRtkAccount] Missing required field: $f for ID: $id. Payload: " . json_encode($accountData));
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

        // Debug logging - log payload before sending to RTK API
        error_log("[updateRtkAccount] Final payload for RTK API (ID: $id): " . json_encode($accountData, JSON_PRETTY_PRINT));

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
    // request first page of 100 without count flag
    $resp = $client->request('GET', '/openapi/stream/stations', [
        'page' => 1,
        'size' => 100
    ]);
    //error_log("RTK API response: " . json_encode($resp));
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
    require_once __DIR__ . '/../../classes/Logger.php';
    
    // Initialize MySQLi connection using defined database constants
    require_once BASE_PATH . '/config/database.php';
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        Logger::error("Kết nối database thất bại: " . $conn->connect_error, ['action' => 'cron_update_stations']);
        return;
    }
    // Set charset to UTF-8
    if (!$conn->set_charset("utf8mb4")) {
        Logger::error("Lỗi thiết lập character set utf8mb4: " . $conn->error, ['action' => 'cron_update_stations']);
    }

    // 1. Lấy danh sách station từ API
    Logger::debug("Bắt đầu lấy danh sách trạm từ API", ['action' => 'cron_update_stations']);
    $map = fetchAllStations();
    if (empty($map)) {
        Logger::warning("Không lấy được danh sách trạm từ API", ['action' => 'cron_update_stations']);
        return;
    }
    Logger::info("Lấy được " . count($map) . " trạm từ API", ['action' => 'cron_update_stations', 'station_count' => count($map)]);
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
        Logger::info("Đánh dấu xóa " . count($toDelete) . " trạm không còn trong API", [
            'action' => 'cron_update_stations', 
            'deleted_count' => count($toDelete),
            'deleted_ids' => $toDelete
        ]);
        $ids = implode(',', $toDelete);
        // Soft-delete: gán status = -1
        $conn->query("UPDATE station SET status = -1 WHERE id IN ($ids)");
    }

    // 4. Thêm mới trạm API chưa có trong DB
    $toInsert = array_diff($apiIds, $existingIds);
    if (!empty($toInsert)) {
        Logger::info("Thêm mới " . count($toInsert) . " trạm từ API", [
            'action' => 'cron_update_stations', 
            'inserted_count' => count($toInsert),
            'new_station_ids' => $toInsert
        ]);
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