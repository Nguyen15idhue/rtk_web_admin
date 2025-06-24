<?php

/**
 * Generates HTML action buttons for an account based on its details.
 *
 * @param array $account Associative array containing account details, including 'id', 'enabled', and 'derived_status'.
 * @return string HTML div containing action buttons.
 */
function get_account_action_buttons(array $account): string {
    $id = htmlspecialchars($account['id'] ?? '');
    if (empty($id)) return ''; // No ID, no buttons

    $status = strtolower($account['derived_status'] ?? 'unknown');
    $isEnabled = isset($account['enabled']) ? (bool)$account['enabled'] : false;
    $buttons = '';

    // View Button (Always available)
    $buttons .= '<button class="btn-icon btn-view" title="Xem" onclick="viewAccountDetails(\'' . $id . '\')"><i class="fas fa-eye"></i></button>';

    // Edit Button
    if (in_array($status, ['active', 'suspended', 'pending', 'expired'])) {
        $buttons .= '<button class="btn-icon btn-edit" title="Sửa" onclick="openEditAccountModal(\'' . $id . '\')" data-permission="account_edit"><i class="fas fa-pencil-alt"></i></button>';
    }

    // Delete Button
    if (!in_array($status, ['active'])) {
        $buttons .= '<button class="btn-icon btn-danger" title="Xóa" onclick="deleteAccount(\'' . $id . '\', event)" data-permission="account_delete"><i class="fas fa-trash-alt"></i></button>';
    }

    // Status‐specific actions
    switch ($status) {
        case 'active':
            $buttons .= '<button class="btn-icon btn-reject" title="Đình chỉ (Disable)" onclick="toggleAccountStatus(\'' . $id . '\', \'suspend\', event)" data-permission="account_status_toggle"><i class="fas fa-ban"></i></button>';
            break;
        case 'suspended':
            $buttons .= '<button class="btn-icon btn-approve" title="Kích hoạt lại (Enable)" onclick="toggleAccountStatus(\'' . $id . '\', \'reactivate\', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle"></i></button>';
            break;
        case 'pending':
            $buttons .= '<button class="btn-icon btn-approve" title="Kích hoạt lại (Enable)" onclick="toggleAccountStatus(\'' . $id . '\', \'reactivate\', event)" data-permission="account_status_toggle"><i class="fas fa-play-circle"></i></button>';
            break;
        case 'unknown':
            error_log("Unknown derived_status ('$status') for account ID: $id in get_account_action_buttons");
            $buttons .= '<button class="btn-icon btn-danger" title="Xóa (Trạng thái không xác định)" onclick="deleteAccount(\'' . $id . '\', event)" data-permission="account_delete"><i class="fas fa-trash-alt"></i></button>';
            break;
        // expired, rejected: no extra buttons
    }

    return '<div class="action-buttons">' . $buttons . '</div>';
}

/**
 * Generate a new survey account username based on a location's province code and next sequence.
 *
 * @param PDO $db Database connection.
 * @param int $locationId ID of the location to derive province_code.
 * @param int $numDigits Number of digits for the sequence (default 3).
 * @return string New survey account username.
 */
function generateSurveyAccountUsername(PDO $db, int $locationId, int $numDigits = 3): string {
    $stmtProv = $db->prepare("SELECT province_code FROM location WHERE id = :loc");
    $stmtProv->bindParam(':loc', $locationId, PDO::PARAM_INT);
    $stmtProv->execute();
    $provinceCode = (string)$stmtProv->fetchColumn();
    if ($provinceCode === '') {
        $provinceCode = 'X'; // Fallback prefix
    }
    // Fix: Get all usernames and find the highest sequence number numerically
    $pattern = $provinceCode . '%';
    $stmtAll = $db->prepare(
        "SELECT username_acc FROM survey_account WHERE username_acc LIKE :pattern"
    );
    $stmtAll->bindParam(':pattern', $pattern, PDO::PARAM_STR);
    $stmtAll->execute();
    $allUsers = $stmtAll->fetchAll(PDO::FETCH_COLUMN);
    
    $maxSeq = 0;
    foreach ($allUsers as $username) {
        // Match any number of digits after the province code
        if (preg_match('/^' . preg_quote($provinceCode, '/') . '(\d+)$/', $username, $matches)) {
            $currentSeq = intval($matches[1]);
            if ($currentSeq > $maxSeq) {
                $maxSeq = $currentSeq;
            }
        }
    }
    
    $seq = $maxSeq + 1;
    return sprintf('%s%0' . $numDigits . 'd', $provinceCode, $seq);
}
