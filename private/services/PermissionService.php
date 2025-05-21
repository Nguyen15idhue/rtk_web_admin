<?php
// filepath: private/services/PermissionService.php

if (!class_exists('PermissionService')) { // Basic guard against multiple inclusions if not using autoloader
    class PermissionService {
        /**
         * Synchronizes permissions in the database.
         * Ensures that all defined permissions exist for key roles ('admin', 'customercare')
         * and any other roles already present in the role_permissions table.
         * Adds missing permissions with a default 'allowed' state of 0 (false).
         *
         * @param PDO $db The database connection object.
         * @param array $all_defined_permissions Associative array of [permission_code => description].
         * @param array $existing_db_roles Numerically indexed array of role keys that exist in the role_permissions table.
         * @return void
         */
        public static function synchronizePermissions(PDO $db, array $all_defined_permissions, array $existing_db_roles): void {
            // Ensure 'admin' and 'customercare' are in the list, then add any other roles from DB
            $roles_to_process = array_unique(array_merge(['admin', 'customercare'], $existing_db_roles));

            if (empty($roles_to_process) || empty($all_defined_permissions)) {
                return; // Nothing to process or no permissions defined
            }

            // Fetch existing permissions for these roles in a single query
            $placeholders = implode(',', array_fill(0, count($roles_to_process), '?'));
            $sql = "SELECT role, permission FROM role_permissions WHERE role IN ($placeholders)";
            $stmt_existing = $db->prepare($sql);
            $stmt_existing->execute($roles_to_process);
            $existing_perms = [];
            while ($row = $stmt_existing->fetch(PDO::FETCH_ASSOC)) {
                $existing_perms[$row['role']][$row['permission']] = true;
            }

            // Prepare insert statement
            $stmt_add_perm = $db->prepare(
                "INSERT INTO role_permissions (role, permission, allowed) VALUES (:role, :permission, 0)"
            );

            // Use transaction for bulk insertion
            $db->beginTransaction();
            foreach ($roles_to_process as $role_to_sync) {
                if (empty($role_to_sync)) continue;
                foreach ($all_defined_permissions as $perm_code => $perm_description) {
                    // Only insert if not already existing
                    if (empty($existing_perms[$role_to_sync][$perm_code])) {
                        $stmt_add_perm->execute([':role' => $role_to_sync, ':permission' => $perm_code]);
                    }
                }
            }
            $db->commit();
        }

        /**
         * Builds a configuration array of pages/permissions grouped by a title,
         * typically used for UI elements like a "create role" modal.
         * It processes a permission group configuration and resolves view/edit codes.
         *
         * @param array $permission_groups_config Associative array [groupTitle => [permission_code1, permission_code2,...]].
         * @param array $all_defined_permissions Associative array [permission_code => description].
         * @return array The structured pagesByGroup configuration.
         */
        public static function buildPagesByGroupConfig(array $permission_groups_config, array $all_defined_permissions): array {
            $pagesByGroup = [];
            foreach ($permission_groups_config as $groupTitle => $codes) {
                $seen_bases_in_group = []; // To avoid duplicate base permissions within the same group
                $group_pages = []; // Accumulate pages for the current group

                foreach ($codes as $code) {
                    if (!isset($all_defined_permissions[$code])) {
                        // If a code in config is not in all_defined_permissions, skip it
                        continue;
                    }

                    $base_perm_code = $code;
                    if (preg_match('/(.+?)_(view|edit)$/', $code, $matches)) {
                        $base_perm_code = $matches[1];
                    }

                    if (in_array($base_perm_code, $seen_bases_in_group)) {
                        // Already processed this base permission (e.g., from its _view or _edit variant)
                        continue;
                    }
                    $seen_bases_in_group[] = $base_perm_code;

                    $viewCode = isset($all_defined_permissions[$base_perm_code . '_view']) ? $base_perm_code . '_view' : null;
                    $editCode = isset($all_defined_permissions[$base_perm_code . '_edit']) ? $base_perm_code . '_edit' : null;
                    
                    // Determine label: Try view's desc, then edit's desc, then base's desc, fallback to base code.
                    $label = $all_defined_permissions[$viewCode] ?? 
                             $all_defined_permissions[$editCode] ?? 
                             $all_defined_permissions[$base_perm_code] ?? 
                             $base_perm_code;

                    $group_pages[] = [
                        'base' => $base_perm_code,
                        'viewCode' => $viewCode,
                        'editCode' => $editCode,
                        'label' => $label,
                    ];
                }
                if (!empty($group_pages)) {
                    $pagesByGroup[$groupTitle] = $group_pages;
                }
            }
            return $pagesByGroup;
        }
    }
}
?>
