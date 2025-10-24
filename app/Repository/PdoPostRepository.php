<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Post as PostEntity;
use Psr\Log\LoggerInterface;
use Throwable;

final class PdoPostRepository implements PostRepositoryInterface
{
    private PDO $db;
    private LoggerInterface $logger;

    public function __construct(PDO $db, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function getAllPosts(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM posts ORDER BY created_at DESC');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            return array_map(function(array $r) {
                return new PostEntity((int)$r['id'], $r['title'], $r['content'], (int)$r['user_id'], $r['created_at']);
            }, $rows);
        } catch (Throwable $e) {
            $this->logger->error('Failed to fetch all posts', ['exception' => $e->getMessage()]);
            return [];
        }
    }

    public function getPost(int $id): ?PostEntity
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM posts WHERE id = ?');
            $stmt->execute([$id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r === false) return null;
            return new PostEntity((int)$r['id'], $r['title'], $r['content'], (int)$r['user_id'], $r['created_at']);
        } catch (Throwable $e) {
            $this->logger->error('Failed to fetch post', ['id' => $id, 'exception' => $e->getMessage()]);
            return null;
        }
    }

    public function createPost(string $title, string $content, int $userId): bool
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)');
            $ok = $stmt->execute([$title, $content, $userId]);
            if ($ok) {
                $this->logger->info('Post inserted', ['title' => $title, 'user_id' => $userId]);
            }
            return $ok;
        } catch (Throwable $e) {
            $this->logger->error('Failed to create post', ['title' => $title, 'user_id' => $userId, 'exception' => $e->getMessage()]);
            return false;
        }
    }
}
