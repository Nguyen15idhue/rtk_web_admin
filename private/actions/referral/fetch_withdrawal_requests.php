<?php
// filepath: private/actions/referral/fetch_withdrawal_requests.php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('referral_management_view'); 
/**
 * Fetch paginated withdrawal requests
 * @param PDO $db
 * @param array $params
 * @return array ['items'=>[], 'total'=>0, 'pages'=>0, 'current'=>1]
 */
function fetch_paginated_withdrawals(PDO $db, array $params) {
    $search = trim($params['search'] ?? '');
    $status = trim($params['status'] ?? '');
    $page = max(1, (int)($params['page'] ?? 1));
    $perPage = DEFAULT_ITEMS_PER_PAGE;
    $offset = ($page-1)*$perPage;

    $where = '1';
    if ($search) {
        $where .= " AND (u.username LIKE :search)";
    }
    if ($status) {
        $where .= " AND wr.status = :status";
    }
    $sql = "SELECT wr.id, wr.user_id, u.username, wr.amount, wr.bank_name,
                   wr.account_number, wr.account_holder, wr.status, wr.created_at
            FROM withdrawal_request wr
            JOIN user u ON u.id=wr.user_id
            WHERE $where
            ORDER BY wr.created_at DESC
            LIMIT :offset, :perPage";
    $stmt = $db->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search', "%$search%");
    }
    if ($status) {
        $stmt->bindValue(':status', $status);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countSql = "SELECT COUNT(*) FROM withdrawal_request wr
                 JOIN user u ON u.id=wr.user_id
                 WHERE $where";
    $countStmt = $db->prepare($countSql);
    if ($search) {
        $countStmt->bindValue(':search', "%$search%");
    }
    if ($status) {
        $countStmt->bindValue(':status', $status);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();
    $pages = ceil($total/$perPage);
    return ['items'=>$items,'total'=>$total,'pages'=>$pages,'current'=>$page];
}
