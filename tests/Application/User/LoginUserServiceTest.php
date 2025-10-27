<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Application\User\LoginUserService;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Infrastructure\Auth\JwtInterface;

final class LoginUserServiceTest extends TestCase
{
    public function testExecuteReturnsJwtToken(): void
    {
        $email = 'joe@example.com';
        $password = 'secret123';

        $user = new User(5, $email, password_hash($password, PASSWORD_BCRYPT));

        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('findUserByEmail')
            ->with($this->equalTo($email))
            ->willReturn($user);

        $jwt = $this->createMock(JwtInterface::class);
        $jwt->expects($this->once())
            ->method('generate')
            ->with($this->callback(fn ($claims) => isset($claims['sub']) && $claims['sub'] === 5 && $claims['email'] === $email))
            ->willReturn('header.payload.sig');

        $service = new LoginUserService($repo, $jwt);

        $token = $service->execute($email, $password);

        $this->assertIsString($token);
        $this->assertSame('header.payload.sig', $token);
    }

    public function testExecuteInvalidInputThrowsException(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $jwt = $this->createMock(JwtInterface::class);
        $service = new LoginUserService($repo, $jwt);

        $this->expectException(\InvalidArgumentException::class);
        $service->execute('', '');
    }
}
