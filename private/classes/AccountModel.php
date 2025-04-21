<?php

class AccountModel {
    private $db;
    private $baseSelectQuery = "
        SELECT
            sa.id, sa.username_acc, sa.enabled, sa.created_at, sa.password_acc, sa.concurrent_user, sa.caster, sa.user_type, sa.regionIds, sa.customerBizType, sa.area,
            r.id as registration_id, r.start_time AS activation_date, r.end_time AS expiry_date, r.status AS registration_status, r.num_account,
            u.id as user_id, u.email AS user_email, u.username AS user_username,
            p.id as package_id, p.name AS package_name, p.package_id AS package_identifier,
            l.id as location_id, l.province AS location_name
        FROM survey_account sa
        JOIN registration r ON sa.registration_id = r.id
        JOIN user u ON r.user_id = u.id
        JOIN package p ON r.package_id = p.id
        JOIN location l ON r.location_id = l.id
        WHERE r.deleted_at IS NULL AND sa.deleted_at IS NULL AND u.deleted_at IS NULL
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
            $sql .= " AND (sa.id LIKE :search OR sa.username_acc LIKE :search OR u.email LIKE :search OR u.username LIKE :search OR l.province LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['package'])) {
            $sql .= " AND p.name = :package_name";
            $params[':package_name'] = $filters['package'];
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
                JOIN package p ON r.package_id = p.id
                JOIN location l ON r.location_id = l.id
                WHERE r.deleted_at IS NULL AND sa.deleted_at IS NULL AND u.deleted_at IS NULL";
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

        $accountId = 'MANUAL_' . $data['registration_id'] . '_' . strtoupper(substr(md5(uniqid()), 0, 6));
        $hashedPassword = password_hash($data['password_acc'], PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            error_log("Create account failed: Password hashing failed.");
            return false;
        }

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
                ':password_acc'     => $hashedPassword,
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
     *                    'username_acc', 'password_acc' (will be hashed if provided),
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
        if (isset($data['password_acc']) && !empty($data['password_acc'])) {
            $hashedPassword = password_hash($data['password_acc'], PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                error_log("Update account ($accountId) failed: Password hashing failed.");
                return false;
            }
            $setClauses[] = "password_acc = :password_acc";
            $params[':password_acc'] = $hashedPassword;
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
            if ($e->getCode() == 23000) {
                error_log("Update account ($accountId) failed: Duplicate username. " . $e->getMessage());
            } else {
                error_log("Update account ($accountId) failed: PDOException. " . $e->getMessage());
            }
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

        $sql = "UPDATE survey_account SET enabled = :enabled, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':enabled', (int)$enable, PDO::PARAM_INT);
            $stmt->bindValue(':id', $accountId, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Toggle status for account ($accountId) to " . ($enable ? 'enabled' : 'disabled') . " failed: " . $e->getMessage());
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
}
