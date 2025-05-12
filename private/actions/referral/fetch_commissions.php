<?php
// filepath: private/actions/referral/fetch_commissions.php

/**
 * Fetch paginated referral commissions
 * @param PDO $db
 * @param array $params
 * @return array ['items'=>[], 'total'=>0, 'pages'=>0, 'current'=>1]
 */
function fetch_paginated_commissions(PDO $db, array $params) {
    $search = trim($params['search'] ?? '');
    $status = trim($params['status'] ?? '');
    $page = max(1, (int)($params['page'] ?? 1));
    $perPage = 15;
    $offset = ($page-1)*$perPage;

    $where = '1';
    if ($search) {
        $where .= " AND (u1.username LIKE :search1 OR u2.username LIKE :search2)";
    }
    if ($status) {
        $where .= " AND rc.status = :status";
    }
    $sql = "SELECT rc.id, rc.referrer_id, u1.username AS referrer_name,
                   rc.referred_user_id, u2.username AS referred_name,
                   rc.transaction_id, rc.commission_amount, rc.status, rc.created_at
            FROM referral_commission rc
            JOIN user u1 ON u1.id=rc.referrer_id
            JOIN user u2 ON u2.id=rc.referred_user_id
            WHERE $where
            ORDER BY rc.created_at DESC
            LIMIT :offset, :perPage";
    $stmt = $db->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search1', "%$search%");
        $stmt->bindValue(':search2', "%$search%");
    }
    if ($status) {
        $stmt->bindValue(':status', $status);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countSql = "SELECT COUNT(*) FROM referral_commission rc
                 JOIN user u1 ON u1.id=rc.referrer_id
                 JOIN user u2 ON u2.id=rc.referred_user_id
                 WHERE $where";
    $countStmt = $db->prepare($countSql);
    if ($search) {
        $countStmt->bindValue(':search1', "%$search%");
        $countStmt->bindValue(':search2', "%$search%");
    }
    if ($status) {
        $countStmt->bindValue(':status', $status);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();
    $pages = ceil($total/$perPage);
    return ['items'=>$items,'total'=>$total,'pages'=>$pages,'current'=>$page];
}
