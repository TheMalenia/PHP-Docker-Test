<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\User as UserEntity;
use Psr\Log\LoggerInterface;
use Throwable;

final class PdoUserRepository implements UserRepositoryInterface
{
    private PDO $db;
    private LoggerInterface $logger;

    public function __construct(PDO $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function createUser(string $email, string $password): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $this->logger->warning('Attempt to create existing user', ['email' => $email]);
                return false;
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
            $ok = $stmt->execute([$email, $hashed]);
            if ($ok) {
                $this->logger->info('User created', ['email' => $email]);
            }
            return $ok;
        } catch (Throwable $e) {
            $this->logger->error('Failed to create user', ['email' => $email, 'exception' => $e->getMessage()]);
            return false;
        }
    }

    public function findUserByEmail(string $email): ?UserEntity
    {
        try {
            $stmt = $this->db->prepare('SELECT id, email, password FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r === false) {
                return null;
            }
            return new UserEntity((int)$r['id'], $r['email'], $r['password']);
        } catch (Throwable $e) {
            $this->logger->error('Failed to find user by email', ['email' => $email, 'exception' => $e->getMessage()]);
            return null;
        }
    }
}
