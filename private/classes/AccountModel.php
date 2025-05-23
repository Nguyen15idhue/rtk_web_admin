<?php
// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', LOGS_PATH . '/error.log');

class AccountModel {
    private $db;
    private $baseSelectQuery = "
        SELECT
            sa.id, sa.username_acc, sa.enabled, sa.created_at, sa.password_acc, sa.concurrent_user,
            sa.caster, sa.user_type, sa.regionIds, sa.customerBizType, sa.area,
            sa.start_time AS activation_date, sa.end_time AS expiry_date,
            r.id as registration_id, r.status AS registration_status, r.num_account,
            u.id as user_id, u.email AS user_email, u.username AS user_username,
            u.phone AS user_phone,
            p.id as package_id, p.name AS package_name, p.package_id AS package_identifier,
            l.id as location_id, l.province AS location_name,
            CASE
                WHEN LOWER(r.status) = 'pending' THEN 'pending'
                WHEN LOWER(r.status) = 'rejected' THEN 'rejected'
                WHEN LOWER(r.status) = 'active' AND sa.enabled = 0 THEN 'suspended'
                WHEN LOWER(r.status) = 'active' AND sa.end_time IS NOT NULL AND sa.end_time < NOW() THEN 'expired'
                WHEN LOWER(r.status) = 'active' THEN 'active'
                ELSE 'unknown'
            END AS derived_status
        FROM survey_account sa
        JOIN registration r ON sa.registration_id = r.id
        JOIN user u ON r.user_id = u.id
        LEFT JOIN package p ON r.package_id = p.id
        JOIN location l ON r.location_id = l.id
        WHERE r.deleted_at IS NULL AND sa.deleted_at IS NULL
    ";

    /**
     * Constructor
     * @param PDO $db PDO database connection object
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Apply filters to the SQL query.
     *
     * @param string $sql The base SQL query string (passed by reference).
     * @param array $params The parameters array (passed by reference).
     * @param array $filters The filters array.
     */
    private function applyFilters(string &$sql, array &$params, array $filters): void {
        if (!empty($filters['search'])) {
            // use CONCAT_WS to search across all relevant fields with one placeholder
            $sql .= " AND CONCAT_WS(' ', sa.id, sa.username_acc, u.email, u.username, l.province) LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['package'])) {
            $sql .= " AND p.id = :package_id";
            $params[':package_id'] = (int)$filters['package'];
        }
        if (!empty($filters['location'])) {
            $sql .= " AND l.id = :location_id";
            $params[':location_id'] = $filters['location'];
        }
        if (!empty($filters['status'])) {
            $filterStatus = strtolower($filters['status']);
            switch ($filterStatus) {
                case 'pending':
                    $sql .= " AND r.status = 'pending'";
                    break;
                case 'rejected':
                    $sql .= " AND r.status = 'rejected'";
                    break;
                case 'suspended':
                    $sql .= " AND r.status = 'active' AND sa.enabled = 0";
                    break;
                case 'expired':
                    $sql .= " AND r.status = 'active' AND sa.end_time IS NOT NULL AND sa.end_time < NOW()";
                    break;
                case 'active':
                    $sql .= " AND r.status = 'active' AND sa.enabled = 1 AND (sa.end_time IS NULL OR sa.end_time >= NOW())";
                    break;
            }
        }
    }

    /**
     * Get the total number of accounts matching the filters.
     *
     * @param array $filters Associative array of filters (e.g., ['search' => '...', 'package' => '...', 'status' => '...'])
     * @return int Total number of matching accounts.
     */
    public function getTotalAccountsCount(array $filters = []): int {
        $sql = "SELECT COUNT(sa.id)
                FROM survey_account sa
                JOIN registration r ON sa.registration_id = r.id
                JOIN user u ON r.user_id = u.id
                LEFT JOIN package p ON r.package_id = p.id
                JOIN location l ON r.location_id = l.id
                WHERE r.deleted_at IS NULL AND sa.deleted_at IS NULL";
        $params = [];

        $this->applyFilters($sql, $params, $filters);

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total accounts count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get a list of accounts matching the filters with pagination.
     *
     * @param array $filters Associative array of filters.
     * @param int $limit Number of items per page.
     * @param int $offset Starting offset for pagination.
     * @return array List of accounts (associative arrays).
     */
    public function getAccounts(array $filters = [], int $limit = 10, int $offset = 0): array {
        $sql = $this->baseSelectQuery;
        $params = [];

        $this->applyFilters($sql, $params, $filters);

        $sql .= " ORDER BY sa.created_at DESC";
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':limit', $params[':limit'], PDO::PARAM_INT);
            $stmt->bindParam(':offset', $params[':offset'], PDO::PARAM_INT);
            foreach ($params as $key => &$val) {
                if (!in_array($key, [':limit', ':offset'])) {
                    $stmt->bindParam($key, $val, PDO::PARAM_STR);
                }
            }
            unset($val);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error fetching accounts: " . $e->getMessage() . "\nSQL: " . $sql . "\nParams: " . print_r($params, true));
            return [];
        }
    }

    /**
     * Get details for a specific account by its ID. Includes related info.
     *
     * @param string $accountId The ID of the account (from survey_account.id).
     * @return array|null Account details as an associative array, or null if not found.
     */
    public function getAccountById(string $accountId): ?array {
        $sql = $this->baseSelectQuery . " AND sa.id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $accountId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching account by ID ($accountId): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new survey account manually.
     * Requires an existing registration ID.
     *
     * @param array $data Associative array containing account data:
     *                    'registration_id' (int, required),
     *                    'username_acc' (string, required),
     *                    'password_acc' (string, required),
     *                    'concurrent_user' (int, optional, default 1),
     *                    'enabled' (bool, optional, default true),
     *                    'caster' (string, optional), 'user_type' (int, optional),
     *                    'regionIds' (int, optional), 'customerBizType' (int, optional, default 1),
     *                    'area' (string, optional),
     *                    'start_time' (string, optional), 'end_time' (string, optional)
     * @return bool True on success, false on failure.
     * @throws PDOException If a database error occurs.
     */
    public function createAccount(array $data): bool {
        if (empty($data['registration_id']) || empty($data['username_acc']) || empty($data['password_acc'])) {
            error_log("Create account failed: Missing required fields.");
            return false;
        }

        // Use provided ID or default to RTK_…
        if (!empty($data['id'])) {
            $accountId = $data['id'];
        } else {
            $accountId = 'RTK_' . $data['registration_id'] . '_' . time();
        }

        // dùng thẳng mật khẩu input
        $password = $data['password_acc'];

        $sql = "INSERT INTO survey_account (
                    id, registration_id, username_acc, password_acc, concurrent_user, enabled,
                    caster, user_type, regionIds, customerBizType, area,
                    start_time, end_time,
                    created_at
                ) VALUES (
                    :id, :registration_id, :username_acc, :password_acc, :concurrent_user, :enabled,
                    :caster, :user_type, :regionIds, :customerBizType, :area,
                    :start_time, :end_time,
                    NOW()
                )";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id'               => $accountId,
                ':registration_id'  => $data['registration_id'],
                ':username_acc'     => $data['username_acc'],
                ':password_acc'     => $password,
                ':concurrent_user'  => $data['concurrent_user'] ?? 1,
                ':enabled'          => isset($data['enabled']) ? (int)$data['enabled'] : 1,
                ':caster'           => $data['caster'] ?? null,
                ':user_type'        => $data['user_type'] ?? null,
                ':regionIds'        => $data['regionIds'] ?? null,
                ':customerBizType'  => $data['customerBizType'] ?? 1,
                ':area'             => $data['area'] ?? null,
                ':start_time'       => $data['start_time'] ?? null,
                ':end_time'         => $data['end_time']   ?? null,
            ]);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Update an existing survey account.
     *
     * @param string $accountId The ID of the account to update.
     * @param array $data Associative array of data to update. Can include:
     *                    'username_acc', 'password_acc',
     *                    'concurrent_user', 'enabled', 'caster', 'user_type',
     *                    'regionIds', 'customerBizType', 'area', 'start_time', 'end_time'.
     *                    Does NOT update 'registration_id'.
     * @return bool True on success, false on failure.
     */
    public function updateAccount(string $accountId, array $data): bool {
        if (empty($accountId) || empty($data)) {
            return false;
        }

        $setClauses = [];
        $params = [':id' => $accountId];

        if (isset($data['username_acc'])) {
            $setClauses[] = "username_acc = :username_acc";
            $params[':username_acc'] = $data['username_acc'];
        }
        if (isset($data['password_acc']) && $data['password_acc'] !== '') {
            $setClauses[] = "password_acc = :password_acc";
            $params[':password_acc'] = $data['password_acc'];
        }
        if (isset($data['concurrent_user'])) {
            $setClauses[] = "concurrent_user = :concurrent_user";
            $params[':concurrent_user'] = (int)$data['concurrent_user'];
        }
        if (isset($data['enabled'])) {
            $setClauses[] = "enabled = :enabled";
            $params[':enabled'] = (int)$data['enabled'];
        }
        if (array_key_exists('caster', $data)) {
            $setClauses[] = "caster = :caster";
            $params[':caster'] = $data['caster'];
        }
        if (array_key_exists('user_type', $data)) {
            $setClauses[] = "user_type = :user_type";
            $params[':user_type'] = $data['user_type'];
        }
        if (array_key_exists('regionIds', $data)) {
            $setClauses[] = "regionIds = :regionIds";
            $params[':regionIds'] = $data['regionIds'];
        }
        if (isset($data['customerBizType'])) {
            $setClauses[] = "customerBizType = :customerBizType";
            $params[':customerBizType'] = (int)$data['customerBizType'];
        }
        if (array_key_exists('area', $data)) {
            $setClauses[] = "area = :area";
            $params[':area'] = $data['area'];
        }
        if (array_key_exists('start_time', $data)) {
            $setClauses[] = "start_time = :start_time";
            $params[':start_time'] = $data['start_time'];
        }
        if (array_key_exists('end_time', $data)) {
            $setClauses[] = "end_time = :end_time";
            $params[':end_time'] = $data['end_time'];
        }

        if (empty($setClauses)) {
            return true;
        }

        $setClauses[] = "updated_at = NOW()";

        $sql = "UPDATE survey_account SET " . implode(', ', $setClauses) . " WHERE id = :id AND deleted_at IS NULL";

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => &$val) {
                $paramType = PDO::PARAM_STR;
                if (in_array($key, [':concurrent_user', ':enabled', ':user_type', ':regionIds', ':customerBizType'])) {
                    $paramType = PDO::PARAM_INT;
                }
                if ($val === null && in_array($key, [':caster', ':user_type', ':regionIds', ':area'])) {
                    $paramType = PDO::PARAM_NULL;
                }
                $stmt->bindValue($key, $val, $paramType);
            }
            unset($val);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log(
                "UpdateAccount failed in " . __METHOD__ .
                ": Code {$e->getCode()}, Message: {$e->getMessage()}" .
                "\nSQL: $sql" .
                "\nParams: " . json_encode($params) .
                "\nTrace:\n" . $e->getTraceAsString()
            );
            return false;
        }
    }

    /**
     * Update the enabled status of a survey account.
     *
     * @param string $accountId The ID of the account.
     * @param bool $enable The desired enabled state (true to enable, false to disable).
     * @return bool True on success, false on failure.
     */
    public function toggleAccountStatus(string $accountId, bool $enable): bool {
        if (empty($accountId)) {
            return false;
        }

        try {
            // 1. Cập nhật survey_account
            $sql1 = "UPDATE survey_account 
                     SET enabled = :enabled, updated_at = NOW() 
                     WHERE id = :id AND deleted_at IS NULL";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->bindValue(':enabled', (int)$enable, PDO::PARAM_INT);
            $stmt1->bindValue(':id', $accountId, PDO::PARAM_STR);
            $stmt1->execute();

            // 2. Nếu đang bật account thì ép luôn registration.status = 'active'
            if ($enable) {
                $sql2 = "UPDATE registration r
                         JOIN survey_account sa ON sa.registration_id = r.id
                         SET r.status = 'active', r.updated_at = NOW()
                         WHERE sa.id = :id AND r.deleted_at IS NULL";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindValue(':id', $accountId, PDO::PARAM_STR);
                $stmt2->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Toggle status for account ($accountId) failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete a survey account by setting the deleted_at timestamp.
     *
     * @param string $accountId The ID of the account to delete.
     * @return bool True on success, false on failure.
     */
    public function deleteAccount(string $accountId): bool {
        if (empty($accountId)) {
            return false;
        }

        $sql = "UPDATE survey_account SET deleted_at = NOW(), enabled = 0, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $accountId, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete account ($accountId) failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete account from database.
     *
     * @param string $accountId
     * @return bool
     */
    public function hardDeleteAccount(string $accountId): bool {
        if (empty($accountId)) {
            return false;
        }
        try {
            $sql = "DELETE FROM survey_account WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $accountId, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Hard delete account ($accountId) failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a username already exists (excluding the given account ID if provided).
     *
     * @param string $username The username to check.
     * @param string|null $excludeAccountId The ID of the account to exclude from the check (for updates).
     * @return bool True if the username exists, false otherwise.
     */
    public function usernameExists(string $username, ?string $excludeAccountId = null): bool {
        $sql = "SELECT COUNT(*) FROM survey_account WHERE username_acc = :username AND deleted_at IS NULL";
        $params = [':username' => $username];

        if ($excludeAccountId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeAccountId;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking username existence: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Get RTK accounts for a specific user, including mount point details.
     *
     * @param int $userId
     * @return array
     */
    public function getAccountsByUserId(int $userId): array {
        try {
            $sql = "SELECT sa.*, 
                           sa.start_time AS start_date, sa.end_time AS end_date,
                           r.status AS reg_status, p.name AS package_name,
                           DATEDIFF(sa.end_time, sa.start_time) AS duration_days,
                           sa.username_acc AS username,
                           r.location_id AS location_id
                    FROM survey_account sa
                    JOIN registration r ON sa.registration_id = r.id
                    LEFT JOIN package p ON r.package_id = p.id
                    WHERE r.user_id = :user_id AND sa.deleted_at IS NULL
                    ORDER BY sa.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $mountPointsByLocation = []; // Initialize here

            // Batch fetch mount points to avoid N+1 queries
            if (!empty($accounts)) {
                // Filter out null location_ids before building the query
                $validLocationIds = array_filter(array_unique(array_column($accounts, 'location_id')), function($id) {
                    return $id !== null;
                });

                if (!empty($validLocationIds)) {
                    $placeholders = implode(',', array_fill(0, count($validLocationIds), '?'));
                    $sqlMp = "SELECT id, ip, port, mountpoint, location_id FROM mount_point WHERE location_id IN ($placeholders)";
                    $stmtMp = $this->db->prepare($sqlMp);
                    $stmtMp->execute(array_values($validLocationIds)); // Use array_values to re-index after filter
                    $mountPoints = $stmtMp->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($mountPoints as $mp) {
                        $mountPointsByLocation[$mp['location_id']][] = [
                            'id' => $mp['id'],
                            'ip' => $mp['ip'],
                            'port' => $mp['port'],
                            'mountpoint' => $mp['mountpoint'],
                        ];
                    }
                }
                unset($mp); // Clean up loop variable

                foreach ($accounts as &$account) {
                    // Ensure 'stations' key exists even if no mount points for that location
                    $account['stations'] = $mountPointsByLocation[$account['location_id']] ?? [];
                }
                unset($account); // Clean up loop variable
            }

            return $accounts;
        } catch (PDOException $e) {
            error_log("Error fetching RTK accounts by user ($userId): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Build RTK API payload for account update.
     *
     * @param string $accountId
     * @param array $input Raw input data from request.
     * @return array Payload ready for updateRtkAccount.
     */
    public function buildRtkUpdatePayload(string $accountId, array $input): array {
        // Lấy thông tin hiện tại
        $account = $this->getAccountById($accountId) ?: [];
        // Mật khẩu
        $pwd = !empty($input['password_acc'])
             ? $input['password_acc']
             : ($account['password_acc'] ?? '');
        // Ngày kích hoạt / hết hạn
        $actDate = $input['activation_date'] ?? ($account['activation_date'] ?? null);
        $expDate = $input['expiry_date']     ?? ($account['expiry_date']     ?? null);
        if ($actDate) {
            // if full datetime provided, use it; otherwise append start-of-day
            $ts = strlen($actDate) > 10 ? strtotime($actDate) : strtotime("$actDate 00:00:00");
            $startMs = $ts * 1000;
        } else {
            $startMs = 0;
        }
        if ($expDate) {
            $ts = strlen($expDate) > 10 ? strtotime($expDate) : strtotime("$expDate 23:59:59");
            $endMs = $ts * 1000;
        } else {
            $endMs = 0;
        }
        // userId trên RTK
        $rtkUserId = $account['user_id'] ?? null;
        // Phone
        $cPhone = $input['customer_phone'] ?? ($account['user_phone'] ?? '');
        $cPhone = preg_replace('/[^0-9+\-]/','',$cPhone);
        $cPhone = substr($cPhone,0,20);
        // Name
        $cName = $input['customer_name'] ?? ($account['user_username'] ?? '');
        // Location + mountIds
        $loc = filter_var($input['location_id'] ?? $account['location_id'], FILTER_VALIDATE_INT) 
               ?: ($account['location_id'] ?? 0);
        $mountIds = getMountPointsByLocationId($loc);
        // Caster/region/company
        $casterIds    = !empty($input['caster'])        ? [trim($input['caster'])] : [];
        $regionIdsArr = isset($input['regionIds'])      ? [(int)$input['regionIds']] : [];
        $custCompany  = $input['customer_company'] ?? '';
        // Các flag khác
        $enabled         = isset($input['enabled'])       ? (int)$input['enabled']       : ($account['enabled'] ?? 1);
        $numOnline       = isset($input['concurrent_user'])? (int)$input['concurrent_user']: ($account['concurrent_user'] ?? 1);
        $customerBizType = isset($input['customerBizType'])? (int)$input['customerBizType']: ($account['customerBizType']  ?? 1);

        return [
            'id'              => $accountId,
            'name'            => $account['username_acc'] ?? '',
            'userPwd'         => $pwd,
            'startTime'       => $startMs,
            'endTime'         => $endMs,
            'enabled'         => $enabled,
            'numOnline'       => $numOnline,
            'customerBizType' => $customerBizType,
            'userId'          => $rtkUserId,
            'customerName'    => $cName,
            'customerPhone'   => $cPhone,
            'customerCompany' => $custCompany,
            'casterIds'       => $casterIds,
            'regionIds'       => $regionIdsArr,
            'mountIds'        => $mountIds,
        ];
    }

    /**
     * Get data by IDs for export.
     *
     * @param array $ids Array of account IDs.
     * @return array List of associative arrays ready for export.
     */
    public function getDataByIdsForExport(array $ids): array {
        if (empty($ids)) {
            return [];
        }
        // build placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = $this->baseSelectQuery . " AND sa.id IN ($placeholders) ORDER BY sa.created_at DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($ids);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getDataByIdsForExport failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all data for export.
     *
     * @return array List of all accounts (associative arrays) ready for export.
     */
    public function getAllDataForExport(): array {
        $sql = $this->baseSelectQuery . " ORDER BY sa.created_at DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllDataForExport failed: " . $e->getMessage());
            return [];
        }
    }
}
