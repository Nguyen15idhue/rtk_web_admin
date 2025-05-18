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
