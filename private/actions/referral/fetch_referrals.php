<?php
// filepath: private/actions/referral/fetch_referrals.php
require_once __DIR__ . '/../../classes/Auth.php';
Auth::ensureAuthorized('referral_management_view'); 
/**
 * Fetch paginated referrals
 * @param PDO $db
 * @param array $params
 * @return array ['items'=>[], 'total'=>0, 'pages'=>0, 'current'=>1]
 */
function fetch_paginated_referrals(PDO $db, array $params) {
    $search = trim($params['search'] ?? '');
    $page = max(1, (int)($params['page'] ?? 1));
    $perPage = DEFAULT_ITEMS_PER_PAGE;
    $offset = ($page-1)*$perPage;

    $where = '1';
    if ($search) {
        $where .= " AND (r.referral_code LIKE :search1 OR u.username LIKE :search2)";
    }
    $sql = "SELECT r.id, r.user_id, u.username, r.referral_code, r.created_at
            FROM referral r
            JOIN user u ON u.id=r.user_id
            WHERE $where
            ORDER BY r.created_at DESC
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

    $countSql = "SELECT COUNT(*) FROM referral r JOIN user u ON u.id=r.user_id WHERE $where";
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
