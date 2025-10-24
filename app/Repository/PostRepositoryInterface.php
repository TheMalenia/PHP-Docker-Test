<?php
declare(strict_types=1);

namespace App\Repository;
use App\Entity\Post;

interface PostRepositoryInterface
{
    /** @return Post[] */
    public function getAllPosts(): array;
    public function getPost(int $id): ?Post;
    public function createPost(string $title, string $content, int $userId): bool;
}
