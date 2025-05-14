<?php
// filepath: private\actions\account\create_account.php
$config = require_once __DIR__ . '/../../core/page_bootstrap.php';

// Use Auth class for authentication and authorization
Auth::ensureAuthorized('account_management');

$db     = $config['db'];
$base   = $config['base_path'];

// Đảm bảo đóng PDO khi script kết thúc
register_shutdown_function(function() use (&$db) {
    $db = null;
});

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort('Invalid request method. Only POST is accepted.', 405);
}

error_log("[CA] Start create_account request");
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
error_log("[CA] Raw input: " . json_encode($input));
if (!is_array($input)) {
    $input = $_POST;
    error_log("Debug: Fallback to \$_POST input: " . json_encode($input));
}

// --- Input Validation ---
$registration_id = filter_var($input['registration_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
// track if we need to seed a registration row
$autoReg = false;
if (!$registration_id) {
    $autoReg = true;
}
$username_acc = trim($input['username_acc'] ?? '');
$password_acc = trim($input['password_acc'] ?? '');

// NEW: support multiple accounts creation
$account_count = filter_var($input['account_count'] ?? 1, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
if ($account_count === false) {
    $account_count = 1;
}

if (empty($username_acc)) {
    abort('Username cannot be empty.', 400);
}
if (empty($password_acc)) {
    abort('Password cannot be empty.', 400);
}

error_log("[CA] registration_id={$registration_id}, username_acc={$username_acc}, password_len=" . strlen($password_acc));

// --- Handle Optional Integer/Boolean Fields ---
// NEW: validate location_id input
$location_input = filter_var($input['location_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
if ($location_input === false) {
    $location_input = 1;
}
// NEW: log để kiểm tra form gửi lên đúng hay không
error_log("[CA] location_input={$location_input}");

// NEW: package_id from form
$package_input = filter_var($input['package_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
if ($package_input === false) $package_input = 1;

// NEW: parse start/end for registration
$rawStartDb = $input['start_time'] ?? 'now';
$rawEndDb   = $input['end_time']   ?? '+30 days';
$dtStartDb  = new DateTime($rawStartDb, new DateTimeZone('Asia/Bangkok'));
$dtEndDb    = new DateTime($rawEndDb,   new DateTimeZone('Asia/Bangkok'));
$start_time_db = $dtStartDb->format('Y-m-d H:i:s');
$end_time_db   = $dtEndDb->format('Y-m-d H:i:s');
error_log("[CA] computed start_time_db={$start_time_db}, end_time_db={$end_time_db}");

$concurrent_user = filter_var($input['concurrent_user'] ?? 1, FILTER_VALIDATE_INT);
if ($concurrent_user === false || $concurrent_user < 1) $concurrent_user = 1; // Default to 1 if invalid or empty

$enabled_input = $input['enabled'] ?? '1'; // Default to '1' if not provided
$enabled = filter_var($enabled_input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 1 : 0; // Ensure 0 or 1

$user_type = filter_var($input['user_type'] ?? null, FILTER_VALIDATE_INT);
if ($user_type === false) $user_type = null; // Set to NULL if empty or invalid

$regionIds = filter_var($input['regionIds'] ?? null, FILTER_VALIDATE_INT);
if ($regionIds === false) $regionIds = null; // Set to NULL if empty or invalid

$customerBizType = filter_var($input['customerBizType'] ?? 1, FILTER_VALIDATE_INT);
if ($customerBizType === false) $customerBizType = 1; // Default to 1 if empty or invalid

// --- XỬ LÝ TRẠNG THÁI ---
$inputStatus = $input['status'] ?? 'active';
// xác định registration.status
$regStatus = in_array($inputStatus, ['pending','rejected']) ? $inputStatus : 'active';
// override enabled: suspended => 0, ngược lại => 1
$enabled = ($inputStatus === 'suspended' || $inputStatus === 'pending') ? 0 : 1;

try {
    $database = Database::getInstance();
    $db = $database->getConnection();

    if (!$db) {
        error_log("Database connection failed in create_account.php"); // Log for admin
        abort('Database connection failed. Please check server configuration.', 500);
    }

    // NEW: fetch user_id based on provided email
    $userEmail = filter_var($input['user_email'] ?? null, FILTER_VALIDATE_EMAIL);
    error_log("[CA] Validating user_email: {$userEmail}");
    if (!$userEmail) {
        abort('Email người dùng không hợp lệ.', 400);
    }
    $stmtUser = $db->prepare("SELECT id FROM `user` WHERE email = ?");
    $stmtUser->execute([$userEmail]);
    $userId = (int)$stmtUser->fetchColumn();
    error_log("[CA] Retrieved userId={$userId}");
    if (!$userId) {
        abort('Không tìm thấy người dùng với email đã cung cấp.', 404);
    }

    // if missing, create a dummy registration so FK will exist
    if ($autoReg) {
        error_log("[CA] Inserting auto registration for user {$userId}");
        $stmt = $db->prepare(
          "INSERT INTO registration 
           (user_id, package_id, location_id, num_account, start_time, end_time, base_price, vat_percent, vat_amount, total_price, status)
           VALUES (?, ?, ?, ?, ?, ?, 0, 0, 0, 0, ?)"
        );
        $stmt->execute([
            $userId,
            $package_input,
            $location_input,
            $account_count,  // use selected number of accounts
            $start_time_db,
            $end_time_db,
            $regStatus       // use mapped status from form
        ]);
        $registration_id = (int)$db->lastInsertId();
        error_log("[CA] auto registration inserted, new regId={$registration_id}");
    }

    // Fetch location_id from registration
    $locStmt = $db->prepare("SELECT location_id FROM registration WHERE id = ?");
    $locStmt->execute([$registration_id]);
    $location_id = (int)$locStmt->fetchColumn();

    // Fetch price & user info to detect trial
    $stmtInfo = $db->prepare("
        SELECT p.price, u.username AS customer_name, u.phone 
        FROM registration r 
        JOIN package p ON r.package_id = p.id 
        JOIN user u    ON r.user_id    = u.id 
        WHERE r.id = ?
    ");
    $stmtInfo->execute([$registration_id]);
    $regInfo = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    error_log("[CA] Price={$regInfo['price']} => " . ((float)$regInfo['price'] === 0.0 ? 'trial' : 'normal') . " flow");

    if ($regInfo && (float)$regInfo['price'] === 0.0) {
        error_log("Debug: Entering trial flow for registration_id={$registration_id}");
        // --- trial flow ---
        // build base username
        $base = preg_replace('/[^a-zA-Z0-9]/', '', $regInfo['customer_name']);
        if ($base === '') $base = 'user' . $_SESSION['admin_id'];
        $username = $base; $i = 1;
        $chk = $db->prepare("SELECT COUNT(*) FROM survey_account WHERE username_acc = ? AND deleted_at IS NULL");
        while (true) {
            $chk->execute([$username]);
            if ($chk->fetchColumn() > 0) {
                $username = $base . $i++;
            } else break;
        }
        $password = bin2hex(random_bytes(8));

        // compute start/end in ms based on GMT+7, not via strtotime
        $rawStart = $input['start_time'] ?? 'now';
        $rawEnd   = $input['end_time']   ?? '+7 days';
        $dtStart  = new DateTime($rawStart, new DateTimeZone('Asia/Bangkok'));
        $dtEnd    = new DateTime($rawEnd,   new DateTimeZone('Asia/Bangkok'));
        $startMs  = $dtStart->getTimestamp() * 1000;
        $endMs    = $dtEnd->getTimestamp()   * 1000;

        // prepare API payload
        $apiData = [
            "name"           => $username,
            "userPwd"        => $password,
            "startTime"      => $startMs,
            "endTime"        => $endMs,
            "enabled"        => $enabled,
            "numOnline"      => $input['concurrent_user'],
            "customerName"   => $regInfo['customer_name'],
            "customerPhone"  => $regInfo['phone'],
            "customerBizType"=> 1,
            "customerCompany"=> "",
            "casterIds"      => [],
            "regionIds"      => [],
            "mountIds"       => getMountPointsByLocationId($location_id)  // <- use helper
        ];

        error_log("[CA] RTK API payload: " . json_encode($apiData));
        $res = createRtkAccount($apiData);
        error_log("[CA] RTK API response: " . json_encode($res));
        if (!$res['success']) {
            abort('RTK API failed: '.($res['error']??''), 500);
        }
        // insert local (trial) – use API-returned ID as the PK
        $accId = (int)$res['data']['id'];    // <-- use API id
        error_log("[CA] Inserting survey_account id={$accId}");
        $ins = $db->prepare("
            INSERT INTO survey_account
              (id,registration_id,username_acc,password_acc,concurrent_user,enabled,customerBizType,created_at)
            VALUES (?,?,?,?,?,?,?,NOW())
        ");
        $ins->execute([
            $accId,
            $registration_id,
            $username,
            $password,
            $concurrent_user,
            $enabled,    // reflect selected status (0 for pending/rejected)
            1            // customerBizType
        ]);
        error_log("[CA] survey_account insert rowCount=" . $ins->rowCount());
        // update registration & transaction
        $db->prepare("UPDATE registration SET status='active',updated_at=NOW() WHERE id=?")
           ->execute([$registration_id]);
        error_log("[CA] registration status updated");
        $db->prepare("
            UPDATE transaction_history 
            SET status='completed',updated_at=NOW() 
            WHERE registration_id=? AND status='pending'
        ")->execute([$registration_id]);
        error_log("[CA] transaction_history status updated");

        api_success(
            ['account' => ['id' => $accId, 'username' => $username, 'password' => $password]],
            'Trial account created'
        );
    }

    error_log("Debug: Non-trial account creation path for registration_id={$registration_id}");
    $accountModel = new AccountModel($db);

    // NEW: prepare for multiple-account loop
    $createdAccounts = [];
    // extract base name and numeric suffix (if any)
    if (preg_match('/^(.*?)(\d+)$/', $username_acc, $m)) {
        $baseName = $m[1];
        $startNum = intval($m[2]);
        $padLen   = strlen($m[2]);
    } else {
        $baseName = $username_acc;
        $startNum = 0;
        $padLen   = 0;
    }

    // ensure starting username does not already exist
    $chkStmt = $db->prepare(
        "SELECT COUNT(*) FROM survey_account WHERE username_acc = ? AND deleted_at IS NULL"
    );
    while (true) {
        // build a candidate username to test
        if ($padLen) {
            $testName = $baseName . str_pad($startNum, $padLen, '0', STR_PAD_LEFT);
        } else {
            $testName = $baseName . $startNum;
        }
        $chkStmt->execute([$testName]);
        if ($chkStmt->fetchColumn() > 0) {
            // already exists → try next
            $startNum++;
            continue;
        }
        break;
    }

    for ($i = 0; $i < $account_count; $i++) {
        // build new username off the first free number
        $num = $startNum + $i;
        if ($padLen) {
            $curUsername = $baseName . str_pad($num, $padLen, '0', STR_PAD_LEFT);
        } else {
            $curUsername = $baseName . $num;
        }

        // --- call RTK API per account ---
        // compute start/end in ms based on GMT+7, not via strtotime
        $rawStart = $input['start_time'] ?? 'now';
        $rawEnd   = $input['end_time']   ?? '+30 days';
        $dtStart  = new DateTime($rawStart, new DateTimeZone('Asia/Bangkok'));
        $dtEnd    = new DateTime($rawEnd,   new DateTimeZone('Asia/Bangkok'));
        $startMs  = $dtStart->getTimestamp() * 1000;
        $endMs    = $dtEnd->getTimestamp()   * 1000;

        $apiData = [
            "name"           => $curUsername,
            "userPwd"        => $password_acc,
            "startTime"      => $startMs,
            "endTime"        => $endMs,
            "enabled"        => $enabled,
            "numOnline"      => $concurrent_user,
            "customerName"   => $input['customer_name']  ?? ($regInfo['customer_name'] ?? ''),
            "customerPhone"  => $input['customer_phone'] ?? ($regInfo['phone']         ?? ''),
            "customerBizType"=> $customerBizType,
            "customerCompany"=> "",
            "casterIds"      => !empty($input['caster']) ? [trim($input['caster'])] : [],
            "regionIds"      => $regionIds ? [$regionIds] : [],
            "mountIds"       => getMountPointsByLocationId($location_id)  // <- use helper
        ];
        error_log("[CA] RTK API payload for {$curUsername}: " . json_encode($apiData));
        $apiRes = createRtkAccount($apiData);
        error_log("[CA] RTK API response for {$curUsername}: " . json_encode($apiRes));
        if (!$apiRes['success']) {
            abort("RTK API failed for {$curUsername}: " . ($apiRes['error'] ?? ''), 500);
        }
        $apiId = (int)$apiRes['data']['id'];

        // --- insert local record via model ---
        $accountData = [
            'id'              => $apiId,
            'registration_id' => $registration_id,
            'username_acc'    => $curUsername,
            'password_acc'    => $password_acc,
            'concurrent_user' => $concurrent_user,
            'enabled'         => $enabled,
            'caster'          => !empty($input['caster']) ? trim($input['caster']) : null,
            'user_type'       => $user_type,
            'regionIds'       => $regionIds,
            'customerBizType' => $customerBizType,
            'area'            => !empty($input['area']) ? trim($input['area']) : null,
            'start_time'      => $start_time_db,
            'end_time'        => $end_time_db,
        ];
        $accountModel->createAccount($accountData);

        $createdAccounts[] = [
            'username' => $curUsername,
            'id'       => $apiId
        ];
    }

    api_success(
        ['accounts' => $createdAccounts],
        'Accounts created: ' . count($createdAccounts)
    );

} catch (PDOException $e) {
    error_log("Database error creating account: " 
        . $e->getMessage() 
        . " (SQLState: " . $e->getCode() . ")" 
        . "\nTrace: " . $e->getTraceAsString() 
        . "\nInput: " . json_encode($input)
    );
    if ($e->getCode() == '23000') {
        $response['message'] = 'Database error: Could not create account due to conflict.';
    } elseif ($e->getCode() == '22001') {
        $response['message'] = 'Database error: Provided data is too long for a field.';
    } elseif ($e->getCode() == 'HY000' && str_contains($e->getMessage(), 'Incorrect integer value')) {
        $response['message'] = 'Database error: Invalid data type provided for a numeric field.';
    } else {
        $response['message'] = 'Database error occurred during account creation.';
    }
    api_error($response['message'], 400);
} catch (Exception $e) {
    error_log("Error creating account: " 
        . $e->getMessage() 
        . "\nTrace: " . $e->getTraceAsString() 
        . "\nInput: " . json_encode($input)
    );
    api_error('An unexpected error occurred: ' . $e->getMessage(), 500);
}
?>
