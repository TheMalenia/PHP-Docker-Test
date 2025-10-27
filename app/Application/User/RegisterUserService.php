<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Repository\UserRepositoryInterface;

//use App\Domain\Exception\UserAlreadyExistsException;

final class RegisterUserService
{
    public function __construct(private UserRepositoryInterface $repo)
    {
    }

    public function execute(string $email, string $password): void
    {
        if (empty($email) || empty($password)) {
            throw new \InvalidArgumentException('Email and password are required.');
        }

        //        if ($this->repo->findUserByEmail($email)) {
        //            throw new UserAlreadyExistsException("User already exists.");
        //        }

        $this->repo->createUser($email, password_hash($password, PASSWORD_BCRYPT));
    }
}
