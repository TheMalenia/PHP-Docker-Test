<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api;

use App\Application\User\RegisterUserService;
use App\Application\User\LoginUserService;
//use App\Domain\Exception\UserAlreadyExistsException;
//use App\Domain\Exception\InvalidCredentialsException;
use App\Infrastructure\Auth\JwtInterface;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use Psr\Log\LoggerInterface;

final class UserApiController
{
    public function __construct(
        private RegisterUserService $registerUser,
        private LoginUserService $loginUser,
        private LoggerInterface $logger
    ) {
    }

    // POST /api/register
    public function register(Request $request): void
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $this->registerUser->execute($data['email'] ?? '', $data['password'] ?? '');

            $this->logger->info('User registered', ['email' => $data['email']]);
            Response::json(['status' => 'created'], 201);

            //        } catch (UserAlreadyExistsException) {
            //            Response::json(['error' => 'User already exists'], 409);
        } catch (\InvalidArgumentException) {
            Response::json(['error' => 'Invalid input'], 422);
        } catch (\Throwable $e) {
            $this->logger->error('Registration failure', ['exception' => $e]);
            Response::json(['error' => 'Server error'], 500);
        }
    }

    // POST /api/login
    public function login(Request $request): void
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $token = $this->loginUser->execute($data['email'] ?? '', $data['password'] ?? '');

            $this->logger->info('User logged in', ['email' => $data['email']]);
            Response::json(['status' => 'ok', 'token' => $token], 200);

            //        } catch (InvalidCredentialsException) {
            //            Response::json(['error' => 'Invalid credentials'], 401);
        } catch (\InvalidArgumentException) {
            Response::json(['error' => 'Invalid input'], 422);
        } catch (\Throwable $e) {
            $this->logger->error('Login failure', ['exception' => $e]);
            Response::json(['error' => 'Server error'], 500);
        }
    }
}
