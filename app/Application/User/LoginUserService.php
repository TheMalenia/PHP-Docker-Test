<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Repository\UserRepositoryInterface;
//use App\Domain\Exception\InvalidCredentialsException;
use App\Infrastructure\Auth\JwtInterface;

final class LoginUserService
{
    public function __construct(
        private UserRepositoryInterface $repo,
        private JwtInterface $jwt
    ) {}

    public function execute(string $email, string $password): string
    {
        if (empty($email) || empty($password)) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        $user = $this->repo->findUserByEmail($email);
//        if (!$user || !password_verify($password, $user->getPasswordHash())) {
//            throw new InvalidCredentialsException("Invalid credentials.");
//        }

        return $this->jwt->generate([
            'sub' => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }
}
