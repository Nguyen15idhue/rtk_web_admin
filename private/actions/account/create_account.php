<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\create_account.php
header('Content-Type: application/json');

// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

// Basic security check
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/AccountModel.php';
require_once __DIR__ . '/../../utils/functions.php'; // For generate_unique_id if needed
require_once __DIR__ . '/../../api/rtk_system/account_api.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        $input = $_POST;
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

    if (empty($username_acc)) {
        $response['message'] = 'Username cannot be empty.';
        echo json_encode($response);
        exit;
    }
    if (empty($password_acc)) {
        $response['message'] = 'Password cannot be empty.';
        echo json_encode($response);
        exit;
    }

    // --- Handle Optional Integer/Boolean Fields ---
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

    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!$db) {
            $response['message'] = 'Database connection failed. Please check server configuration.';
            error_log("Database connection failed in create_account.php"); // Log for admin
            echo json_encode($response);
            exit;
        }

        // if missing, create a dummy registration so FK will exist
        if ($autoReg) {
            $stmt = $db->prepare(
              "INSERT INTO registration 
               (user_id, package_id, location_id, num_account, start_time, end_time, base_price, vat_percent, vat_amount, total_price, status)
               VALUES (?, 1, 1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0, 0, 0, 0, 'pending')"
            );
            $stmt->execute([ $_SESSION['admin_id'] ]);
            $registration_id = (int)$db->lastInsertId();
        }

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

        if ($regInfo && (float)$regInfo['price'] === 0.0) {
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

            // prepare API payload
            $apiData = [
                "name"           => $username,
                "userPwd"        => $password,
                "startTime"      => strtotime($input['start_time'] ?? 'now')*1000,
                "endTime"        => strtotime($input['end_time']   ?? '+7 days')*1000,
                "enabled"        => 1,
                "numOnline"      => $input['concurrent_user'],
                "customerName"   => $regInfo['customer_name'],
                "customerPhone"  => $regInfo['phone'],
                "customerBizType"=> 1,
                "customerCompany"=> "",
                "casterIds"      => [],
                "regionIds"      => [],
                "mountIds"       => []
            ];

            $res = createRtkAccount($apiData);
            if (!$res['success']) {
                echo json_encode(['success'=>false,'message'=>'RTK API failed: '.($res['error']??'')]);
                exit;
            }
            // insert local
            $accId = 'RTK_'.$registration_id.'_'.time();
            $ins = $db->prepare("
                INSERT INTO survey_account
                  (id,registration_id,username_acc,password_acc,concurrent_user,enabled,customerBizType,created_at)
                VALUES (?,?,?,?,?,?,?,NOW())
            ");
            $ins->execute([
                $accId,
                $registration_id,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $concurrent_user,
                1,
                1
            ]);
            // update registration & transaction
            $db->prepare("UPDATE registration SET status='active',updated_at=NOW() WHERE id=?")
               ->execute([$registration_id]);
            $db->prepare("
                UPDATE transaction_history 
                SET status='completed',updated_at=NOW() 
                WHERE registration_id=? AND status='pending'
            ")->execute([$registration_id]);

            echo json_encode([
                'success'  => true,
                'message'  => 'Trial account created',
                'account'  => ['username'=>$username,'password'=>$password]
            ]);
            exit;
        }

        $accountModel = new AccountModel($db);

        // --- Always call RTK API for new account ---
        try {
            $apiData = [
                "name"           => $username_acc,
                "userPwd"        => $password_acc,
                "startTime"      => strtotime($input['start_time'] ?? 'now') * 1000,
                "endTime"        => strtotime($input['end_time']   ?? '+30 days') * 1000,
                "enabled"        => $enabled,
                "numOnline"      => $concurrent_user,
                "customerName"   => $input['customer_name']  ?? ($regInfo['customer_name'] ?? ''),
                "customerPhone"  => $input['customer_phone'] ?? ($regInfo['phone']         ?? ''),
                "customerBizType"=> $customerBizType,
                "customerCompany"=> "",
                "casterIds"      => !empty($input['caster']) ? [trim($input['caster'])] : [],
                "regionIds"      => $regionIds ? [$regionIds] : [],
                "mountIds"       => []
            ];
            $apiRes = createRtkAccount($apiData);
            if (!$apiRes['success']) {
                echo json_encode(['success'=>false,'message'=>'RTK API error: '.$apiRes['error']]);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'message'=>'RTK API exception: '.$e->getMessage()]);
            exit;
        }

        // Check if username already exists
        if ($accountModel->usernameExists($username_acc)) {
            $response['message'] = 'Username "' . htmlspecialchars($username_acc) . '" already exists.';
            echo json_encode($response);
            exit;
        }

        // --- Generate RTK_ ID for survey_account table ---
        $unique_account_id = 'RTK_' . $registration_id . '_' . time();

        // Prepare data for creation
        $accountData = [
            'id'               => $unique_account_id,
            'registration_id'  => $registration_id,
            'username_acc'     => $username_acc,
            'password_acc'     => $password_acc, // raw, will hash next
            'concurrent_user'  => $concurrent_user,
            'enabled'          => $enabled,
            'caster'           => !empty($input['caster']) ? trim($input['caster']) : null,
            'user_type'        => $user_type,
            'regionIds'        => $regionIds,
            'customerBizType'  => $customerBizType,
            'area'             => !empty($input['area']) ? trim($input['area']) : null,
        ];

        // Hash password
        $accountData['password_acc'] = password_hash($accountData['password_acc'], PASSWORD_DEFAULT);

        // Create account
        $creationSuccess = false;
        try {
            $creationSuccess = $accountModel->createAccount($accountData);
        } catch (PDOException $e) {
            // show detailed DB error
            echo json_encode(['success'=>false,'message'=>'DB error: '.$e->getMessage()]);
            exit;
        }

        if ($creationSuccess) {
            echo json_encode([
                'success' => true,
                'message' => 'Account created with ID: ' . htmlspecialchars($unique_account_id)
            ]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Unknown failure']);
        }
        exit;

    } catch (PDOException $e) {
        error_log("Database error creating account: " . $e->getMessage() . " - SQL State: " . $e->getCode());
        if ($e->getCode() == '23000') {
             $response['message'] = 'Database error: Could not create account due to a data conflict (e.g., username might already exist).';
        } elseif ($e->getCode() == '22001') {
             $response['message'] = 'Database error: Provided data is too long for a field.';
        } elseif ($e->getCode() == 'HY000' && str_contains($e->getMessage(), 'Incorrect integer value')) {
             $response['message'] = 'Database error: Invalid data type provided for a numeric field.';
        } else {
             $response['message'] = 'Database error occurred during account creation.';
        }
    } catch (Exception $e) {
        error_log("Error creating account: " . $e->getMessage());
        $response['message'] = 'An unexpected error occurred: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method. Only POST is accepted.';
}

echo json_encode($response);
