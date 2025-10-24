<?php

declare(strict_types=1);

namespace App\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}

final class SimpleContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $entries = [];

    public function set(string $id, $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service not found: $id");
        }

        $entry = $this->entries[$id];

        // If it's a factory (callable), call it and cache result
        if (is_callable($entry)) {
            try {
                $result = $entry($this);
                $this->entries[$id] = $result;
                return $result;
            } catch (\Throwable $e) {
                throw new ContainerException($e->getMessage(), 0, $e);
            }
        }

        return $entry;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
