<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Post
{
    private int $id;
    private string $title;
    private string $content;
    private int $userId;
    private string $createdAt;

    public function __construct(int $id, string $title, string $content, int $userId, string $createdAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
