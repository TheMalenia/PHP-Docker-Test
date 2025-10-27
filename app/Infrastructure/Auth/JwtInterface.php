<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

interface JwtInterface
{
    /**
     * Generate a JWT token from given claims.
     *
     * @param array $claims
     * @return string
     */
    public function generate(array $claims): string;

    /**
     * Validate a token and return its payload or null if invalid.
     *
     * @param string $token
     * @return array|null
     */
    public function validate(string $token): ?array;
}
