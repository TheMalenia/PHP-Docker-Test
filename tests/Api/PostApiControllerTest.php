<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\Api\PostApiController;
use App\Http\Request;
use App\Entity\Post as PostEntity;
use App\Repository\PostRepositoryInterface;
use App\Auth\Jwt;
use Psr\Log\LoggerInterface;

final class PostApiControllerTest extends TestCase
{
    public function testIndexOutputsPosts(): void
    {
        $post = new PostEntity(1, 't', 'c', 2, '2020-01-01');

        $repo = $this->createMock(PostRepositoryInterface::class);
        $repo->method('getAllPosts')->willReturn([$post]);

        $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new PostApiController($repo, $jwt, $logger);

        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/posts']);
        $ctrl->index($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('"id":1', $out);
        $this->assertStringContainsString('"title":"t"', $out);
    }

    public function testShowNotFoundReturns404(): void
    {
        $repo = $this->createMock(PostRepositoryInterface::class);
        $repo->method('getPost')->willReturn(null);

        $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new PostApiController($repo, $jwt, $logger);

        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/api/posts/5']);
        $ctrl->show($req, 5);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('Not found', $out);
        $this->assertSame(404, http_response_code());
    }

    public function testCreateMissingAuthReturns401(): void
    {
    $repo = $this->createMock(PostRepositoryInterface::class);
    $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new PostApiController($repo, $jwt, $logger);

        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/posts']);
        $ctrl->create($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('Authorization required', $out);
        $this->assertSame(401, http_response_code());
    }

    public function testCreateInvalidTokenReturns401(): void
    {
        $repo = $this->createMock(PostRepositoryInterface::class);
        $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new PostApiController($repo, $jwt, $logger);

        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/posts', 'HTTP_AUTHORIZATION' => 'Bearer bad']);
        $ctrl->create($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('Invalid token', $out);
        $this->assertSame(401, http_response_code());
    }

    public function testCreateSuccessReturns201(): void
    {
        $repo = $this->createMock(PostRepositoryInterface::class);
        $repo->method('createPost')->willReturn(true);

        $jwt = new Jwt('test-secret');

        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new PostApiController($repo, $jwt, $logger);

        $token = $jwt->generate(['sub' => 7]);
        $server = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/posts', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token];
        // simulate parsed body by constructing Request with $post param
        $post = ['title' => 'Hi', 'content' => 'Hello'];
        ob_start();
        $req = new Request($server, [], $post);
        $ctrl->create($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('created', $out);
        $this->assertSame(201, http_response_code());
    }
}
