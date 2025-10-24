<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Repository\UserRepositoryInterface;
use App\Http\Request;
use App\Http\Response;
use App\Auth\Jwt;
use Psr\Log\LoggerInterface;

final class UserApiController
{
    private UserRepositoryInterface $repo;
    private Jwt $jwt;
    private LoggerInterface $logger;

    public function __construct(UserRepositoryInterface $repo, Jwt $jwt, LoggerInterface $logger)
    {
        $this->repo = $repo;
        $this->jwt = $jwt;
        $this->logger = $logger;
    }

    // POST /api/register
    public function register(Request $request)
    {
        $body = $request->getParsedBody() ?: [];
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::json(['error' => 'Invalid input'], 422);
            return;
        }

        $ok = $this->repo->createUser($email, $password);
        if ($ok) {
            $this->logger->info('User registered', ['email' => $email]);
            Response::json(['status' => 'created'], 201);
        } else {
            $this->logger->warning('Attempt to register existing user', ['email' => $email]);
            Response::json(['error' => 'User exists'], 409);
        }
    }

    // POST /api/login
    public function login(Request $request)
    {
        $body = $request->getParsedBody() ?: [];
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::json(['error' => 'Invalid input'], 422);
            return;
        }

        $user = $this->repo->findUserByEmail($email);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            // return JWT token
            $token = $this->jwt->generate(['sub' => $user->getId(), 'email' => $user->getEmail()]);
            $this->logger->info('User logged in', ['email' => $email, 'user_id' => $user->getId()]);
            Response::json(['status' => 'ok', 'token' => $token]);
            return;
        }

        $this->logger->warning('Invalid login attempt', ['email' => $email]);
        Response::json(['error' => 'Invalid credentials'], 401);
    }
}
