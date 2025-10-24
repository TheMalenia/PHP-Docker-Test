<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\Api\UserApiController;
use App\Http\Request;
use App\Entity\User as UserEntity;
use App\Repository\UserRepositoryInterface;
use App\Auth\Jwt;
use Psr\Log\LoggerInterface;

final class UserApiControllerTest extends TestCase
{
    public function testRegisterSuccessReturns201(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('createUser')->willReturn(true);

        $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new UserApiController($repo, $jwt, $logger);

        $post = ['email' => 'a@b.com', 'password' => 'pass'];
        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/register'], [], $post);
        $ctrl->register($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('created', $out);
        $this->assertSame(201, http_response_code());
    }

    public function testRegisterConflictReturns409(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('createUser')->willReturn(false);

    $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new UserApiController($repo, $jwt, $logger);

        $post = ['email' => 'a@b.com', 'password' => 'pass'];
        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/register'], [], $post);
        $ctrl->register($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('User exists', $out);
        $this->assertSame(409, http_response_code());
    }

    public function testLoginSuccessReturnsToken(): void
    {
        $password = 'mypassword';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $user = new UserEntity(12, 'me@x.com', $hash);

        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findUserByEmail')->willReturn($user);

        $jwt = new Jwt('test-secret');

        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new UserApiController($repo, $jwt, $logger);

        $post = ['email' => 'me@x.com', 'password' => $password];
        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/login'], [], $post);
        $ctrl->login($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('token', $out);
    }

    public function testLoginInvalidCredentialsReturns401(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findUserByEmail')->willReturn(null);

        $jwt = new Jwt('test-secret');
        $logger = $this->createMock(LoggerInterface::class);

        $ctrl = new UserApiController($repo, $jwt, $logger);

        $post = ['email' => 'x@y.com', 'password' => 'nope'];
        ob_start();
        $req = new Request(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api/login'], [], $post);
        $ctrl->login($req);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('Invalid credentials', $out);
        $this->assertSame(401, http_response_code());
    }
}
