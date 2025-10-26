<?php
declare(strict_types=1);

namespace App\Application\Post;

use App\Domain\Repository\PostRepositoryInterface;

final class GetAllPostsService
{
    public function __construct(
        private PostRepositoryInterface $posts
    ) {}

    public function execute(): array
    {
        return $this->posts->getAllPosts();
    }
}