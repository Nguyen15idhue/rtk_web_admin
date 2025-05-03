<?php
// Prevent PHP from outputting HTML errors directly
error_reporting(E_ALL); // Report all errors for logging
ini_set('display_errors', 0); // Keep off for browser output
ini_set('log_errors', 1); // Ensure errors are logged
ini_set('error_log', 'E:\Application\laragon\www\rtk_web_admin\private\logs\error.log');

class AccountModel {
    private $db;
    private $baseSelectQuery = "
        SELECT
            sa.id, sa.username_acc, sa.enabled, sa.created_at, sa.password_acc, sa.concurrent_user, sa.caster, sa.user_type, sa.regionIds, sa.customerBizType, sa.area,
            r.id as registration_id, r.start_time AS activation_date, r.end_time AS expiry_date, r.status AS registration_status, r.num_account,
            u.id as user_id, u.email AS user_email, u.username AS user_username,
            u.phone AS user_phone,                                             
            p.id as package_id, p.name AS package_name, p.package_id AS package_identifier,
            l.id as location_id, l.province AS location_name
        FROM survey_account sa
        JOIN registration r ON sa.registration_id = r.id
        JOIN user u ON r.user_id = u.id
        LEFT JOIN package p ON r.package_id = p.id
        JOIN location l ON r.location_id = l.id
        WHERE r.deleted_at IS NULL AND sa.deleted_at IS NULL 
    "; // Updated base query to include more fields for editing

    /**
     * Constructor
     * @param PDO $db PDO database connection object
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Derive account status based on registration status, enabled flag, and expiry date.
     *
     * @param string $registrationStatus Status from the registration table.
     * @param bool $enabled Enabled flag from the survey_account table.
     * @param string|null $expiryDate Expiry date string.
     * @return string Derived status ('active', 'pending', 'expired', 'suspended', 'rejected', 'unknown').
     */
    private function deriveAccountStatus(string $registrationStatus, bool $enabled, ?string $expiryDate): string {
        $regStatusLower = strtolower($registrationStatus);

        // Priority 1: Registration status overrides everything else
        if ($regStatusLower === 'pending') return 'pending'; // Chờ KH
        if ($regStatusLower === 'rejected') return 'rejected'; // Bị từ chối

        // Priority 2: If registration is active, check enabled and expiry
        if ($regStatusLower === 'active') {
            if (!$enabled) return 'suspended'; // Đình chỉ (if explicitly disabled by admin)
            if ($expiryDate && strtotime($expiryDate) < time()) return 'expired'; // Hết hạn
            return 'active'; // Hoạt động (enabled and not expired)
        }

        // Fallback for any unexpected registration status
        error_log("Unknown registration status encountered: " . $registrationStatus);
        return 'unknown';
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
                    $sql .= " AND r.status = 'active' AND r.end_time IS NOT NULL AND r.end_time < NOW()";
                    break;
                case 'active':
                    $sql .= " AND r.status = 'active' AND sa.enabled = 1 AND (r.end_time IS NULL OR r.end_time >= NOW())";
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
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($accounts as &$account) {
                $isEnabled = isset($account['enabled']) ? (bool)$account['enabled'] : false;
                $account['derived_status'] = $this->deriveAccountStatus(
                    $account['registration_status'] ?? 'unknown',
                    $isEnabled,
                    $account['expiry_date']
                );
            }
            unset($account);

            return $accounts;

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
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account) {
                $account['derived_status'] = $this->deriveAccountStatus(
                    $account['registration_status'],
                    (bool)$account['enabled'],
                    $account['expiry_date']
                );
            }

            return $account ?: null;
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
     *                    'area' (string, optional)
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
                    caster, user_type, regionIds, customerBizType, area, created_at
                ) VALUES (
                    :id, :registration_id, :username_acc, :password_acc, :concurrent_user, :enabled,
                    :caster, :user_type, :regionIds, :customerBizType, :area, NOW()
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
     *                    'regionIds', 'customerBizType', 'area'.
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
            $sql = "SELECT sa.*, r.start_time AS start_date, r.end_time AS end_date,"
                 . " r.status AS reg_status, p.name AS package_name,"
                 . " DATEDIFF(r.end_time, r.start_time) AS duration_days,"
                 . " sa.username_acc AS username"
                 . " FROM survey_account sa"
                 . " JOIN registration r ON sa.registration_id = r.id"
                 . " LEFT JOIN package p ON r.package_id = p.id"
                 . " WHERE r.user_id = :user_id AND sa.deleted_at IS NULL"
                 . " ORDER BY sa.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($accounts as &$account) {
                $account['stations'] = $this->getMountPointsForAccount($account['id']);
            }
            unset($account);

            return $accounts;
        } catch (PDOException $e) {
            error_log("Error fetching RTK accounts by user ($userId): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch mount point entries for a given account.
     *
     * @param string $accountId
     * @return array
     */
    private function getMountPointsForAccount(string $accountId): array {
        try {
            $sql = "SELECT mp.id, mp.ip, mp.port, mp.mountpoint"
                 . " FROM survey_account sa"
                 . " JOIN registration r ON sa.registration_id = r.id"
                 . " JOIN mount_point mp ON mp.location_id = r.location_id"
                 . " WHERE sa.id = :account_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':account_id', $accountId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error fetching mount points for account ' . $accountId . ': ' . $e->getMessage());
            return [];
        }
    }

    // Giải phóng kết nối DB khi object bị hủy
    public function __destruct() {
        $this->db = null;
    }
}
