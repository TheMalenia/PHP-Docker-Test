<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Container\SimpleContainer;
use App\Infrastructure\Container\NotFoundException;

final class SimpleContainerTest extends TestCase
{
    public function testSetGetHas(): void
    {
        $c = new SimpleContainer();
        $c->set('value', 42);

        $this->assertTrue($c->has('value'));
        $this->assertSame(42, $c->get('value'));
    }

    public function testFactoryIsCalledOnceAndCached(): void
    {
        $c = new SimpleContainer();

        $c->set('obj', function($container) {
            $o = new stdClass();
            $o->time = microtime(true);
            return $o;
        });

        $a = $c->get('obj');
        $b = $c->get('obj');

    $this->assertSame($a, $b);
    $this->assertTrue(property_exists($a, 'time'));
    }

    public function testGetMissingThrows(): void
    {
        $this->expectException(NotFoundException::class);
        $c = new SimpleContainer();
        $c->get('nope');
    }
}
