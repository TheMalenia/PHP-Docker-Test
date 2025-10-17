<?php
require_once __DIR__ . '/../config/Database.php';

class Post {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllPosts() {
        $posts = $this->db->query("SELECT * FROM posts ORDER BY created_at DESC");
        return $posts->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPost($id) {
        $post = $this->db->prepare("SELECT * FROM posts WHERE id=?");
        $post->execute([$id]);
        return $post->fetch(PDO::FETCH_ASSOC);
    }

    public function createPost($title, $content, $userId) {
        $stmt = $this->db->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $content, $userId]);
    }
}
?>
