<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Post;

interface PostRepositoryInterface
{
    /** @return Post[] */
    public function getAllPosts(): array;
    public function getPost(int $id): ?Post;
    public function createPost(string $title, string $content, int $userId): bool;
}
