<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api;

use App\Application\Post\GetAllPostsService;
use App\Application\Post\GetPostByIdService;
use App\Application\Post\CreatePostService;
//use App\Domain\Exception\PostNotFoundException;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Infrastructure\Auth\Jwt;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

final class PostApiController
{
    public function __construct(
        private GetAllPostsService $getAllPosts,
        private GetPostByIdService $getPostById,
        private CreatePostService $createPost,
        private Jwt $jwt,
        private LoggerInterface $logger
    ) {}

    // GET /api/posts
    public function index(Request $request): void
    {
        $this->logger->info('Fetching all posts via API');

        $posts = $this->getAllPosts->execute();

        $result = array_map(fn($p) => [
            'id'         => $p->getId(),
            'title'      => $p->getTitle(),
            'content'    => $p->getContent(),
            'user_id'    => $p->getUserId(),
            'created_at' => $p->getCreatedAt(),
        ], $posts);

        Response::json($result);
    }

    // GET /api/posts/{id}
    public function show(Request $request, int $id): void
    {
        $this->logger->info('Fetching post', ['id' => $id]);

//        try {
            $post = $this->getPostById->execute($id);
//        } catch (PostNotFoundException) {
//            $this->logger->warning('Post not found', ['id' => $id]);
//            Response::json(['error' => 'Not found'], 404);
//            return;
//        }

        Response::json([
            'id'         => $post->getId(),
            'title'      => $post->getTitle(),
            'content'    => $post->getContent(),
            'user_id'    => $post->getUserId(),
            'created_at' => $post->getCreatedAt(),
        ]);
    }

    // POST /api/posts
    public function create(Request $request): void
    {
        $body = $request->getParsedBody() ?? [];
        $title = $body['title'] ?? '';
        $content = $body['content'] ?? '';
        $userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;

        // Authorization
        $auth = $request->getHeader('Authorization') ?? '';
        if (!str_starts_with($auth, 'Bearer ')) {
            $this->logger->warning('Missing Authorization header on create');
            Response::json(['error' => 'Authorization required'], 401);
            return;
        }

        $token = substr($auth, 7);
        $payload = $this->jwt->validate($token);

        if (!$payload || !isset($payload['sub'])) {
            $this->logger->warning('Invalid token on create', ['auth' => $auth]);
            Response::json(['error' => 'Invalid token'], 401);
            return;
        }

        if ($userId <= 0) {
            $userId = (int) $payload['sub'];
        }

        try {
            $this->createPost->execute($title, $content, $userId);
        } catch (InvalidArgumentException $e) {
            Response::json(['error' => $e->getMessage()], 422);
            return;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create post', ['exception' => $e]);
            Response::json(['error' => 'Failed to create'], 500);
            return;
        }

        $this->logger->info('Post created', ['title' => $title, 'user_id' => $userId]);
        Response::json(['status' => 'created'], 201);
    }
}
