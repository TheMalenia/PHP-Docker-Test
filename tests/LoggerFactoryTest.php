<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

final class LoggerFactoryTest extends TestCase
{
    public function testCreateReturnsPsrLogger(): void
    {
        // ensure environment variables are predictable
        putenv('LOG_DESTINATION=stdout');
        putenv('LOG_LEVEL=INFO');

        $logger = LoggerFactory::create();

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
