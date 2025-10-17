<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
    private $postModel;

    public function __construct() {
        $this->postModel = new Post();
    }

    public function index() {
        $posts = $this->postModel->getAllPosts();
        include __DIR__ . '/../views/posts/index.php';
    }

    public function show($id) {
        $post = $this->postModel->getPost($id);
        include __DIR__ . '/../views/posts/show.php';
    }

    public function create() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $userId = $_SESSION['user_id'];

            if ($this->postModel->createPost($title, $content, $userId)) {
                header("Location: index.php?page=post");
            } else {
                $error = "Failed to create post.";
                include __DIR__ . '/../views/posts/create.php';
            }
        } else {
            include __DIR__ . '/../views/posts/create.php';
        }
    }
}
?>
