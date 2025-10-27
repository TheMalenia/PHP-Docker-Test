<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Application\User\RegisterUserService;
use App\Domain\Repository\UserRepositoryInterface;

final class RegisterUserServiceTest extends TestCase
{
    public function testExecuteCallsCreateUserWithHashedPassword(): void
    {
        $email = 'joe@example.com';
        $password = 'secret123';

        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('createUser')
            ->with(
                $this->equalTo($email),
                $this->callback(fn($hash) => password_verify($password, $hash))
            )
            ->willReturn(true);

        $service = new RegisterUserService($repo);

        // Should not throw and should call createUser with a valid hash
        $service->execute($email, $password);
        $this->addToAssertionCount(1); // ensure at least one assertion
    }

    public function testExecuteInvalidInputThrowsException(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $service = new RegisterUserService($repo);

        $this->expectException(\InvalidArgumentException::class);
        $service->execute('', '');
    }
}
