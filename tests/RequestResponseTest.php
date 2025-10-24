<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Http\Request;
use App\Http\Response;

final class RequestResponseTest extends TestCase
{
    public function testRequestParsesMethodPathAndHeadersAndBodyFromPost(): void
    {
        $server = [
            'REQUEST_METHOD' => 'post',
            'REQUEST_URI' => '/api/items?x=1',
            'HTTP_X_TEST_HEADER' => 'value',
        ];
        $get = ['x' => '1'];
        $post = ['name' => 'alice'];

        $req = new Request($server, $get, $post);

        $this->assertSame('POST', $req->getMethod());
        $this->assertSame('/api/items', $req->getPath());
        $this->assertSame($get, $req->getQueryParams());
        $this->assertSame($post, $req->getParsedBody());
        $this->assertSame('value', $req->getHeader('X-Test-Header'));
    }

    public function testResponseJsonOutputsEncodedJson(): void
    {
        $data = ['ok' => true, 'name' => 'bob'];

        ob_start();
        Response::json($data, 201);
        $out = ob_get_clean();

        $this->assertJson($out);
        $this->assertStringContainsString('"ok":true', $out);
        $this->assertStringContainsString('"name":"bob"', $out);
    }
}
