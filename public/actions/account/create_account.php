<?php
// filepath: e:\Application\laragon\www\rtk_web_admin\private\actions\account\create_account.php
header('Content-Type: application/json');
session_start();

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

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    // --- Input Validation ---
    $registration_id = filter_var($input['registration_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $username_acc   = trim($input['username_acc']   ?? '');
    $password_acc   = trim($input['password_acc']   ?? '');

    if (!$registration_id) {
        // Auto‑generate a unique registration_id when missing
        $registration_id = time();
    }
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

        $accountModel = new AccountModel($db);

        // Check if username already exists
        if ($accountModel->usernameExists($username_acc)) {
             $response['message'] = 'Username "' . htmlspecialchars($username_acc) . '" already exists.';
             echo json_encode($response);
             exit;
        }

        // --- Generate Unique ID for survey_account table ---
        $unique_account_id = uniqid('ACC_', true);

        // Prepare data for creation
        $accountData = [
            'id' => $unique_account_id, // The new Varchar ID for survey_account
            'registration_id' => $registration_id, // The validated integer FK
            'username_acc' => $username_acc,
            'password_acc' => $password_acc, // Consider hashing the password here or in the model
            'concurrent_user' => $concurrent_user,
            'enabled' => $enabled,
            'caster' => !empty($input['caster']) ? trim($input['caster']) : null,
            'user_type' => $user_type,
            'regionIds' => $regionIds,
            'customerBizType' => $customerBizType,
            'area' => !empty($input['area']) ? trim($input['area']) : null,
        ];

        // Hash the password before saving (BEST PRACTICE)
        if (!empty($accountData['password_acc'])) {
             $accountData['password_acc'] = password_hash($accountData['password_acc'], PASSWORD_DEFAULT);
        } else {
             $response['message'] = 'Password cannot be empty.';
             echo json_encode($response);
             exit;
        }

        $creationSuccess = $accountModel->createAccount($accountData);

        if ($creationSuccess) {
            $response['success'] = true;
            $response['message'] = 'Account created successfully with ID: ' . htmlspecialchars($unique_account_id);
        } else {
            $response['message'] = 'Failed to create account. The operation did not complete successfully. Please check input data or server logs.';
            $logData = $accountData;
            unset($logData['password_acc']); // Don't log hashed password
            error_log("Failed to create account with data: " . json_encode($logData));
        }

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
