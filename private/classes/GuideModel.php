<?php
// load the Database singleton
require_once __DIR__ . '/Database.php';

// private/classes/GuideModel.php
class GuideModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getAll($search = '') {
        $sql = "SELECT g.*, a.name AS author_name
                FROM guide g
                LEFT JOIN admin a ON g.author_id=a.id
                WHERE g.title LIKE ? OR g.topic LIKE ?
                ORDER BY g.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $like = "%$search%";
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOne($id) {
        $sql = "SELECT g.*, a.name AS author_name
                FROM guide g
                LEFT JOIN admin a ON g.author_id = a.id
                WHERE g.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        // tự sinh slug nếu trống
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $data['title']), '-'));
        }
        $sql = "INSERT INTO guide(title, slug, content, author_id, topic, status, thumbnail, image)
                VALUES(?,?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'], 
            $data['slug'], 
            $data['content'], 
            $data['author_id'], 
            $data['topic'], 
            $data['status'], 
            $data['thumbnail'] ?? null, 
            $data['image'] ?? null
        ]);
    }
    public function update($id, $data) {
        // tự sinh slug nếu trống
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $data['title']), '-'));
        }
        $sql = "UPDATE guide SET title=?, slug=?, content=?, topic=?, status=?, thumbnail=?, image=?, published_at=?
                WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['topic'],
            $data['status'],
            $data['thumbnail'] ?? null,
            $data['image'] ?? null,
            $data['published_at'] ?? null,
            $id
        ]);
    }
    public function delete($id) {
        $sql = "DELETE FROM guide WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    public function toggleStatus($id, $status) {
        $sql = "UPDATE guide SET status=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}