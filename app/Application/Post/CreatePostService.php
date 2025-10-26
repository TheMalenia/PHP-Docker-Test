<?php
declare(strict_types=1);

namespace App\Application\Post;

use App\Domain\Repository\PostRepositoryInterface;
use InvalidArgumentException;

final class CreatePostService
{
    public function __construct(
        private PostRepositoryInterface $posts
    ) {}

    public function execute(string $title, string $content, int $userId): bool
    {
        $title = trim($title);
        $content = trim($content);

        if ($title === '' || $content === '' || $userId <= 0) {
            throw new InvalidArgumentException('Invalid input for creating a post.');
        }

        return $this->posts->createPost($title, $content, $userId);
    }
}
