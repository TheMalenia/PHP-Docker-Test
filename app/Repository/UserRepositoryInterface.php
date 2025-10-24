<?php
declare(strict_types=1);

namespace App\Repository;
use App\Entity\User;

interface UserRepositoryInterface
{
    public function createUser(string $email, string $password): bool;
    public function findUserByEmail(string $email): ?User;
}
