<?php
namespace App\Controllers;

use App\Repository\PostRepositoryInterface;
use Psr\Log\LoggerInterface;

class PostController {
    private PostRepositoryInterface $postRepository;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $postRepository, LoggerInterface $logger)
    {
        $this->postRepository = $postRepository;
        $this->logger = $logger;
    }

    public function index() {
        $this->logger->info('Rendering posts index');
        $posts = $this->postRepository->getAllPosts();
        include __DIR__ . '/../Views/posts/index.php';
    }

    public function show($id) {
        $this->logger->info('Rendering post show', ['id' => $id]);
        $post = $this->postRepository->getPost((int)$id);
        include __DIR__ . '/../Views/posts/show.php';
    }

    public function create() {
        if (!isset($_SESSION['user'])) {
                header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $userId = $_SESSION['user_id'];

            if ($this->postRepository->createPost($title, $content, (int)$userId)) {
                $this->logger->info('Post created via web controller', ['title' => $title, 'user_id' => $userId]);
                header("Location: /");
            } else {
                $this->logger->error('Failed to create post via web controller', ['title' => $title, 'user_id' => $userId]);
                $error = "Failed to create post.";
                include __DIR__ . '/../Views/posts/create.php';
            }
        } else {
            include __DIR__ . '/../Views/posts/create.php';
        }
    }
}

