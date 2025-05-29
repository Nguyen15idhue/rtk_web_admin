<?php
require_once __DIR__ . '/../config/constants.php';
require_once BASE_PATH . '/classes/Database.php';

// private/classes/GuideModel.php
class GuideModel {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function getAll($search = '', $topic = '', $status = '') {
        $sql = "SELECT g.*, a.name AS author_name
                FROM guide g
                LEFT JOIN admin a ON g.author_id=a.id
                WHERE (g.title LIKE ? OR g.topic LIKE ?)";
        $params = [];
        $like = "%$search%";
        $params[] = $like;
        $params[] = $like;
        if (!empty($topic)) {
            $sql .= " AND g.topic = ?";
            $params[] = $topic;
        }
        if (!empty($status)) {
            $sql .= " AND g.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY g.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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

    /**
     * Properly transliterate UTF-8 strings to ASCII slugs
     */
    protected function slugify($string) {
        if (empty($string)) {
            return 'n-a';
        }

        // Ensure string is UTF-8 and convert to lowercase
        // Requires the mbstring PHP extension
        $string = mb_strtolower((string)$string, 'UTF-8');

        // Normalize (decompose) characters and remove diacritics if intl extension is available
        // Requires the intl PHP extension for Normalizer class
        if (class_exists('Normalizer') && defined('Normalizer::FORM_D')) {
            $string = \Normalizer::normalize($string, \Normalizer::FORM_D);
            $string = preg_replace('/[\x{0300}-\x{036f}]/u', '', $string);
        } else {
            // Fallback for systems without intl extension:
            // Basic transliteration for common Vietnamese characters.
            $charMap = [
                // Vietnamese vowels (lowercase)
                'à'=>'a', 'á'=>'a', 'ả'=>'a', 'ã'=>'a', 'ạ'=>'a',
                'ă'=>'a', 'ằ'=>'a', 'ắ'=>'a', 'ẳ'=>'a', 'ẵ'=>'a', 'ặ'=>'a',
                'â'=>'a', 'ầ'=>'a', 'ấ'=>'a', 'ẩ'=>'a', 'ẫ'=>'a', 'ậ'=>'a',
                'đ'=>'d', // Handled separately below as well, but good to have in map
                'è'=>'e', 'é'=>'e', 'ẻ'=>'e', 'ẽ'=>'e', 'ẹ'=>'e',
                'ê'=>'e', 'ề'=>'e', 'ế'=>'e', 'ể'=>'e', 'ễ'=>'e', 'ệ'=>'e',
                'ì'=>'i', 'í'=>'i', 'ỉ'=>'i', 'ĩ'=>'i', 'ị'=>'i',
                'ò'=>'o', 'ó'=>'o', 'ỏ'=>'o', 'õ'=>'o', 'ọ'=>'o',
                'ô'=>'o', 'ồ'=>'o', 'ố'=>'o', 'ổ'=>'o', 'ỗ'=>'o', 'ộ'=>'o',
                'ơ'=>'o', 'ờ'=>'o', 'ớ'=>'o', 'ở'=>'o', 'ỡ'=>'o', 'ợ'=>'o',
                'ù'=>'u', 'ú'=>'u', 'ủ'=>'u', 'ũ'=>'u', 'ụ'=>'u',
                'ư'=>'u', 'ừ'=>'u', 'ứ'=>'u', 'ử'=>'u', 'ữ'=>'u', 'ự'=>'u',
                'ỳ'=>'y', 'ý'=>'y', 'ỷ'=>'y', 'ỹ'=>'y', 'ỵ'=>'y',
            ];
            $string = strtr($string, $charMap);
        }

        // Ensure 'đ' is converted to 'd' (it's handled by strtr if intl is not used and 'đ' is in charMap)
        $string = str_replace('đ', 'd', $string);

        // Replace non-alphanumeric characters (now that we have mostly ASCII-like chars) with a hyphen
        // Allows letters (a-z), numbers (0-9)
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);

        // Replace multiple hyphens with a single hyphen
        $string = preg_replace('/-+/', '-', $string);

        // Trim hyphens from the start and end of the string
        $string = trim($string, '-');

        // If the string becomes empty after transformations
        return empty($string) ? 'n-a' : $string;
    }

    public function create($data) {
        // generate slug from title with proper transliteration
        $data['slug'] = $this->slugify($data['title']);
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
        if (!empty($data['title'])) {
            $data['slug'] = $this->slugify($data['title']);
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

    /**
     * Lấy danh sách chủ đề hiện có từ bảng guide
     * @return array Danh sách tên chủ đề
     */
    public function getDistinctTopics(): array {
        $sql = "SELECT DISTINCT topic FROM guide WHERE topic IS NOT NULL AND topic <> ''";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'topic');
    }
    
    /**
     * Đếm tổng số guides theo điều kiện filter
     */
    public function getCount($search = '', $topic = '', $status = '') {
        $sql = "SELECT COUNT(*) as total
                FROM guide g
                WHERE (g.title LIKE ? OR g.topic LIKE ?)";
        $params = [];
        $like = "%$search%";
        $params[] = $like;
        $params[] = $like;
        
        if (!empty($topic)) {
            $sql .= " AND g.topic = ?";
            $params[] = $topic;
        }
        if (!empty($status)) {
            $sql .= " AND g.status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Lấy guides với pagination từ database level
     */
    public function getPaginated($search = '', $topic = '', $status = '', $limit = 10, $offset = 0) {
        $sql = "SELECT g.*, a.name AS author_name
                FROM guide g
                LEFT JOIN admin a ON g.author_id=a.id
                WHERE (g.title LIKE ? OR g.topic LIKE ?)";
        $params = [];
        $like = "%$search%";
        $params[] = $like;
        $params[] = $like;
        
        if (!empty($topic)) {
            $sql .= " AND g.topic = ?";
            $params[] = $topic;
        }
        if (!empty($status)) {
            $sql .= " AND g.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY g.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}