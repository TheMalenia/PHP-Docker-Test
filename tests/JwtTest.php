<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Auth\Jwt;

final class JwtTest extends TestCase
{
    public function testGenerateAndValidate(): void
    {
        $jwt = new Jwt('test-secret');

        $token = $jwt->generate(['sub' => '123', 'role' => 'user']);

        $this->assertIsString($token);

        $payload = $jwt->validate($token);

        $this->assertIsArray($payload);
        $this->assertSame('123', $payload['sub']);
        $this->assertSame('user', $payload['role']);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
    }

    public function testTamperedTokenFailsValidation(): void
    {
        $jwt = new Jwt('another-secret');
        $token = $jwt->generate(['sub' => '1']);

        // tamper payload (flip a char)
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
        $parts[1][0] = $parts[1][0] === 'A' ? 'B' : 'A';
        $tampered = implode('.', $parts);

        $this->assertNull($jwt->validate($tampered));
    }
}
