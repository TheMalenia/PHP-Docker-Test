<?php

declare(strict_types=1);

namespace App\Presentation\Http;

final class Request
{
    private array $server;
    private array $get;
    private array $post;
    private $body;

    public function __construct(array $server = [], array $get = [], array $post = [])
    {
        $this->server = $server ?: $_SERVER;
        $this->get = $get ?: $_GET;
        $this->post = $post ?: $_POST;

        $raw = file_get_contents('php://input');
        $decoded = null;
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
        }
        $this->body = $decoded ?? null;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPath(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public function getQueryParams(): array
    {
        return $this->get;
    }

    public function getParsedBody(): mixed
    {
        return $this->body ?? $this->post;
    }

    public function getHeader(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }
}
