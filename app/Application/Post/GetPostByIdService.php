<?php

declare(strict_types=1);

namespace App\Application\Post;

use App\Domain\Repository\PostRepositoryInterface;

//use App\Domain\Exception\PostNotFoundException;

final class GetPostByIdService
{
    public function __construct(
        private PostRepositoryInterface $posts
    ) {
    }

    public function execute(int $id)
    {
        $post = $this->posts->getPost($id);

        //        if ($post === null) {
        //            throw new PostNotFoundException(sprintf('Post with id %d not found', $id));
        //        }

        return $post;
    }
}
