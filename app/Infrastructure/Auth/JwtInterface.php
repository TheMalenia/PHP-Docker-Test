<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

interface JwtInterface
{
    public function generate(array $claims): string;

    public function validate(string $token): ?array;
}
