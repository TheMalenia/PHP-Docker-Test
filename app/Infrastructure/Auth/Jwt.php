<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

final class Jwt implements JwtInterface
{
    private string $secret;
    private int $ttl; // seconds

    public function __construct(string $secret = '')
    {
        $this->secret = $secret ?: (getenv('JWT_SECRET') ?: 'dev-secret-change-me');
        $this->ttl = 3600; // 1 hour
    }

    public function generate(array $claims): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $now = time();
        $payload = array_merge([
            'iat' => $now,
            'exp' => $now + $this->ttl,
        ], $claims);

        $segments = [];
        $segments[] = $this->base64UrlEncode(json_encode($header));
        $segments[] = $this->base64UrlEncode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = $this->sign($signingInput);
        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    public function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$h64, $p64, $s64] = $parts;

        $header = json_decode($this->base64UrlDecode($h64), true);
        $payload = json_decode($this->base64UrlDecode($p64), true);
        $signature = $this->base64UrlDecode($s64);

        if (!is_array($payload) || !is_array($header)) {
            return null;
        }
        if (($header['alg'] ?? '') !== 'HS256') {
            return null;
        }

        $signingInput = $h64 . '.' . $p64;
        $expected = $this->sign($signingInput);
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $now = time();
        if (isset($payload['exp']) && $payload['exp'] < $now) {
            return null;
        }
        if (isset($payload['nbf']) && $payload['nbf'] > $now) {
            return null;
        }

        return $payload;
    }

    private function sign(string $input): string
    {
        return hash_hmac('sha256', $input, $this->secret, true);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
