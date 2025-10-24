<?php
declare(strict_types=1);

namespace App\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public static function create(): LoggerInterface
    {
        $logger = new Logger('app');

        // Add a UID processor to help correlate logs
        $logger->pushProcessor(new UidProcessor());

        // Configure destination
        $dest = getenv('LOG_DESTINATION') ?: 'stdout';
        $level = self::levelFromEnv(getenv('LOG_LEVEL') ?: 'DEBUG');

        // Resolve destination to a stream path
        $stream = self::resolveDestination($dest);

        $handler = new StreamHandler($stream, $level);
        $logger->pushHandler($handler);

        return $logger;
    }

    private static function resolveDestination(string $dest): string
    {
        $d = trim($dest);

        if (strtolower($d) === 'stdout') {
            return 'php://stdout';
        }

        if (strtolower($d) === 'stderr') {
            return 'php://stderr';
        }

        // allow file:prefix or direct path
        if (str_starts_with($d, 'file:')) {
            return substr($d, 5) ?: 'php://stdout';
        }

        // otherwise treat as file path
        return $d;
    }

    private static function levelFromEnv(string $level): int
    {
        switch (strtoupper($level)) {
            case 'DEBUG':
                return Logger::DEBUG;
            case 'INFO':
                return Logger::INFO;
            case 'NOTICE':
                return Logger::NOTICE;
            case 'WARNING':
            case 'WARN':
                return Logger::WARNING;
            case 'ERROR':
                return Logger::ERROR;
            case 'CRITICAL':
                return Logger::CRITICAL;
            case 'ALERT':
                return Logger::ALERT;
            case 'EMERGENCY':
            case 'EMERG':
                return Logger::EMERGENCY;
            default:
                return Logger::DEBUG;
        }
    }
}
