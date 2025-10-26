<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function createUser(string $email, string $password): bool;
    public function findUserByEmail(string $email): ?User;
}
