<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../classes/Auth.php'; // Include the Auth class

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

        // API endpoint
        $url = 'http://203.171.25.138:8090/openapi/broadcast/users';

        // Generate headers
        $nonce = bin2hex(random_bytes(16));
        $timestamp = (string)(round(microtime(true) * 1000));
        $accessKey = 'Zb5F6iKUuAISy4qY';
        $secretKey = 'KL1KEEJj2s6HA8LB';
        $signMethod = 'HmacSHA256';

        $headers = [
            'X-Nonce' => $nonce,
            'X-Access-Key' => $accessKey,
            'X-Sign-Method' => $signMethod,
            'X-Timestamp' => $timestamp
        ];

        // Calculate signature
        $method = 'POST';
        $uri = '/openapi/broadcast/users';
        $signStr = "$method $uri ";
        ksort($headers);
        foreach ($headers as $key => $value) {
            $signStr .= strtolower($key) . "=" . $value . "&";
        }
        $signStr = rtrim($signStr, "&");
        
        $sign = hash_hmac('sha256', $signStr, $secretKey);
        $headers['Sign'] = $sign;
        $headers['Content-Type'] = 'application/json';

        // Prepare cURL request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($accountData));

        // Set headers
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        // disable SSL verification for self-signed certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'data' => null,
                'error' => "cURL Error: $curlError"
            ];
        }

        $responseData = json_decode($response, true);

        // Check response
        if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['code']) && 
            ($responseData['code'] === 'SUCCESS' || $responseData['code'] === 'OK')) {
            return [
                'success' => true,
                'data' => $responseData['data'] ?? $responseData,
                'error' => null
            ];
        }

        return [
            'success' => false,
            'data' => $responseData,
            'error' => $responseData['msg'] ?? "HTTP Error: $httpCode"
        ];

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

        // Endpoint and signature setup
        $url       = "http://203.171.25.138:8090/openapi/broadcast/users/{$id}";
        $method    = 'PUT';
        $uri       = "/openapi/broadcast/users/{$id}";
        $nonce     = bin2hex(random_bytes(16));
        $timestamp = (string)round(microtime(true) * 1000);
        $accessKey = 'Zb5F6iKUuAISy4qY';
        $secretKey = 'KL1KEEJj2s6HA8LB';
        $signMethod= 'HmacSHA256';

        $headers = [
            'X-Nonce'       => $nonce,
            'X-Access-Key'  => $accessKey,
            'X-Sign-Method' => $signMethod,
            'X-Timestamp'   => $timestamp,
        ];
        ksort($headers);

        // Build sign string
        $signStr = "$method $uri ";
        foreach ($headers as $k => $v) {
            $signStr .= strtolower($k) . "=" . $v . "&";
        }
        $sign = hash_hmac('sha256', rtrim($signStr, "&"), $secretKey);
        $headers['Sign']          = $sign;
        $headers['Content-Type']  = 'application/json';

        // cURL setup
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($accountData));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $curlH = [];
        foreach ($headers as $k => $v) {
            $curlH[] = "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlH);

        $resp     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'data' => null, 'error' => "cURL Error: $err"];
        }

        $data = json_decode($resp, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($data['code']) &&
           ($data['code'] === 'SUCCESS' || $data['code'] === 'OK')) {
            return ['success' => true, 'data' => $data['data'] ?? $data, 'error' => null];
        }

        return ['success' => false, 'data' => $data, 'error' => $data['msg'] ?? "HTTP Error: $httpCode"];
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
    $url       = 'http://203.171.25.138:8090/openapi/broadcast/users/delete';
    $method    = 'POST';
    $uri       = '/openapi/broadcast/users/delete';
    $secretKey = 'KL1KEEJj2s6HA8LB';
    $accessKey = 'Zb5F6iKUuAISy4qY';
    $signMethod= 'HmacSHA256';
    $nonce     = bin2hex(random_bytes(16));
    $timestamp = (string)round(microtime(true)*1000);

    $headers = [
        'X-Nonce'       => $nonce,
        'X-Access-Key'  => $accessKey,
        'X-Sign-Method' => $signMethod,
        'X-Timestamp'   => $timestamp,
    ];
    ksort($headers);

    // build sign string
    $signStr = "$method $uri ";
    foreach ($headers as $k => $v) {
        $signStr .= strtolower($k)."=".$v."&";
    }
    $sign    = hash_hmac('sha256', rtrim($signStr, "&"), $secretKey);
    $headers['Sign']         = $sign;
    $headers['Content-Type'] = 'application/json';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_POST              => true,
        CURLOPT_POSTFIELDS        => json_encode(['ids'=>$ids]),
        CURLOPT_HTTPHEADER        => array_map(fn($k,$v)=>"$k: $v", array_keys($headers), $headers),
        CURLOPT_CONNECTTIMEOUT    => 5,
        CURLOPT_TIMEOUT           => 15,
    ]);
    $resp     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['success'=>false,'data'=>null,'error'=>"cURL Error: $err"];
    }
    $data = json_decode($resp, true);
    if ($httpCode>=200 && $httpCode<300 && isset($data['code']) 
        && in_array($data['code'], ['SUCCESS','OK'], true)) {
        return ['success'=>true,'data'=>$data['data']??$data,'error'=>null];
    }
    return ['success'=>false,'data'=>$data,'error'=>$data['msg'] ?? "HTTP $httpCode"];
}

/**
 * GET list of stations (id→identificationName, lat, lng).
 */
function fetchAllStations(): array {
    $url = 'http://203.171.25.138:8090/openapi/stream/stations?page=1&size=100&count=true';
    // build and sign headers (reuse your X-Nonce/X-Timestamp logic)
    $nonce     = bin2hex(random_bytes(16));
    $timestamp = (string)round(microtime(true)*1000);
    $accessKey = 'Zb5F6iKUuAISy4qY';
    $secretKey = 'KL1KEEJj2s6HA8LB';
    $signMethod= 'HmacSHA256';
    $method = 'GET';
    $uri    = '/openapi/stream/stations';
    $hdr = ['X-Nonce'=>$nonce,'X-Access-Key'=>$accessKey,'X-Sign-Method'=>$signMethod,'X-Timestamp'=>$timestamp];
    ksort($hdr);
    $signStr = "$method $uri ";
    foreach($hdr as $k=>$v) $signStr .= strtolower($k)."=$v&";
    $hdr['Sign']=hash_hmac('sha256',rtrim($signStr,'&'),$secretKey);
    $curlH=[];
    foreach($hdr as $k=>$v) $curlH[]="$k: $v";
    $ch = curl_init("$url");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_HTTPHEADER=>$curlH]);
    $resp = curl_exec($ch); curl_close($ch);
    $j = json_decode($resp,true);
    $out = [];
    foreach($j['data']['records']??[] as $r){
        $out[(int)$r['id']] = [
            'stationName'         => $r['stationName']         ?? '',
            'identificationName'  => $r['identificationName']  ?? '',
            'lat'                 => $r['lat']                 ?? null,
            'lng'                 => $r['lng']                 ?? null,
        ];
    }
    return $out;
}

/**
 * POST dynamic-info with array of ids.
 */
function fetchStationDynamicInfo(array $ids): array {
    $url = 'http://203.171.25.138:8090/openapi/stream/stations/dynamic-info';
    // generate & sign headers as above but METHOD=POST and URI='/openapi/stream/stations/dynamic-info'
    $nonce     = bin2hex(random_bytes(16));
    $timestamp = (string)round(microtime(true)*1000);
    $accessKey = 'Zb5F6iKUuAISy4qY';
    $secretKey = 'KL1KEEJj2s6HA8LB';
    $signMethod= 'HmacSHA256';
    $method = 'POST';
    $uri    = '/openapi/stream/stations/dynamic-info';
    $hdr = ['X-Nonce'=>$nonce,'X-Access-Key'=>$accessKey,'X-Sign-Method'=>$signMethod,'X-Timestamp'=>$timestamp];
    ksort($hdr);
    $signStr = "$method $uri ";
    foreach($hdr as $k=>$v) $signStr .= strtolower($k)."=$v&";
    $hdr['Sign']=hash_hmac('sha256',rtrim($signStr,'&'),$secretKey);
    $hdr['Content-Type']='application/json';
    $curlH=[];
    foreach($hdr as $k=>$v) $curlH[]="$k: $v";
    $ch = curl_init($url);
    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER=>1,
        CURLOPT_POST=>1,
        CURLOPT_POSTFIELDS=>json_encode(['ids'=>$ids]),
        CURLOPT_HTTPHEADER=>$curlH
    ]);
    $resp = curl_exec($ch); curl_close($ch);
    $j = json_decode($resp,true);
    return $j['data'] ?? [];
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