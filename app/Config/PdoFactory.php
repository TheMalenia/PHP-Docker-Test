<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

final class PdoFactory
{
    public static function createFromEnv(): PDO
    {
        $host = getenv('DB_HOST') ?: 'db';
        $name = getenv('DB_NAME') ?: 'blog';
        $user = getenv('DB_USER') ?: 'user';
        $pass = getenv('DB_PASS') ?: 'pass';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }
}
