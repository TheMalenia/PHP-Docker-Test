<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Repository\PostRepositoryInterface;
use App\Http\Request;
use App\Http\Response;
use App\Auth\Jwt;
use Psr\Log\LoggerInterface;

final class PostApiController
{
    private PostRepositoryInterface $repo;
    private Jwt $jwt;
    private LoggerInterface $logger;

    public function __construct(PostRepositoryInterface $repo, Jwt $jwt, LoggerInterface $logger)
    {
        $this->repo = $repo;
        $this->jwt = $jwt;
        $this->logger = $logger;
    }

    // GET /api/posts
    public function index(Request $request)
    {
        $this->logger->info('Fetching all posts via API');
        $posts = $this->repo->getAllPosts();
        Response::json(array_map(function($p){
            return [
                'id' => $p->getId(),
                'title' => $p->getTitle(),
                'content' => $p->getContent(),
                'user_id' => $p->getUserId(),
                'created_at' => $p->getCreatedAt(),
            ];
        }, $posts));
    }

    // GET /api/posts/{id}
    public function show(Request $request, $id)
    {
        $this->logger->info('Fetching post', ['id' => $id]);
        $post = $this->repo->getPost((int)$id);
        if (!$post) {
            $this->logger->warning('Post not found', ['id' => $id]);
            Response::json(['error' => 'Not found'], 404);
            return;
        }
        Response::json([
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'user_id' => $post->getUserId(),
            'created_at' => $post->getCreatedAt(),
        ]);
    }

    // POST /api/posts
    public function create(Request $request)
    {
        $body = $request->getParsedBody() ?: [];
        $title = $body['title'] ?? '';
        $content = $body['content'] ?? '';
        $userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;

        // Require Authorization: Bearer <token>
        $auth = $request->getHeader('Authorization') ?: '';
        if (strpos($auth, 'Bearer ') === 0) {
            $token = trim(substr($auth, 7));
            $payload = $this->jwt->validate($token);
            if ($payload && isset($payload['sub'])) {
                // use subject as user id if not provided
                if ($userId <= 0) {
                    $userId = (int)$payload['sub'];
                }
            } else {
                $this->logger->warning('Invalid token on create', ['auth' => $auth]);
                Response::json(['error' => 'Invalid token'], 401);
                return;
            }
        } else {
            $this->logger->warning('Missing Authorization header on create');
            Response::json(['error' => 'Authorization required'], 401);
            return;
        }

        if (empty($title) || empty($content) || $userId <= 0) {
            Response::json(['error' => 'Invalid input'], 422);
            return;
        }

        $ok = $this->repo->createPost($title, $content, $userId);
        if ($ok) {
            $this->logger->info('Post created', ['title' => $title, 'user_id' => $userId]);
            Response::json(['status' => 'created'], 201);
        } else {
            $this->logger->error('Failed to create post', ['title' => $title, 'user_id' => $userId]);
            Response::json(['error' => 'Failed to create'], 500);
        }
    }
}
