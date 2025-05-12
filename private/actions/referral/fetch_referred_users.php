<?php
// filepath: private/actions/referral/fetch_referred_users.php

/**
 * Fetch paginated referred users list
 * @param PDO $db
 * @param array $params
 * @return array ['items'=>[], 'total'=>0, 'pages'=>0, 'current'=>1]
 */
function fetch_paginated_referred_users(PDO $db, array $params) {
    $search = trim($params['search'] ?? '');
    $page = max(1, (int)($params['page'] ?? 1));
    $perPage = 15;
    $offset = ($page-1)*$perPage;

    $where = '1';
    if ($search) {
        $where .= " AND (u1.username LIKE :search1 OR u2.username LIKE :search2)";
    }
    $sql = "SELECT ru.id, ru.referrer_id, u1.username AS referrer_name,
                   ru.referred_user_id, u2.username AS referred_name,
                   ru.created_at
            FROM referred_user ru
            JOIN user u1 ON u1.id=ru.referrer_id
            JOIN user u2 ON u2.id=ru.referred_user_id
            WHERE $where
            ORDER BY ru.created_at DESC
            LIMIT :offset, :perPage";
    $stmt = $db->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search1', "%$search%");
        $stmt->bindValue(':search2', "%$search%");
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countSql = "SELECT COUNT(*) FROM referred_user ru
                 JOIN user u1 ON u1.id=ru.referrer_id
                 JOIN user u2 ON u2.id=ru.referred_user_id
                 WHERE $where";
    $countStmt = $db->prepare($countSql);
    if ($search) {
        $countStmt->bindValue(':search1', "%$search%");
        $countStmt->bindValue(':search2', "%$search%");
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();
    $pages = ceil($total/$perPage);
    return ['items'=>$items,'total'=>$total,'pages'=>$pages,'current'=>$page];
}
