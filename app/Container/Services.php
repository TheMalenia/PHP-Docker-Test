<?php
declare(strict_types=1);

namespace App\Container;

use App\Config\PdoFactory;
use App\Repository\PostRepositoryInterface;
use App\Repository\PdoPostRepository;
use App\Repository\UserRepositoryInterface;
use App\Repository\PdoUserRepository;
use App\Auth\Jwt;
use App\Controllers\Api\PostApiController;
use App\Controllers\Api\UserApiController;
use Psr\Log\LoggerInterface;
use App\Logger\LoggerFactory;
use App\Controllers\PostController;
use App\Controllers\UserController;

final class Services
{
    public static function register($container): void
    {
        $container->set(PdoFactory::class, function() {
            static $instance = null;
            if ($instance === null) {
                $instance = PdoFactory::createFromEnv();
            }
            return $instance;
        });

        $container->set(PostRepositoryInterface::class, function($c) {
            return new PdoPostRepository($c->get(PdoFactory::class), $c->get(LoggerInterface::class));
        });

        $container->set(UserRepositoryInterface::class, function($c) {
            return new PdoUserRepository($c->get(PdoFactory::class), $c->get(LoggerInterface::class));
        });

        $container->set(Jwt::class, function($c) {
            return new Jwt(getenv('JWT_SECRET') ?: 'dev-secret-change-me');
        });

        // PSR logger (singleton)
        $container->set(LoggerInterface::class, function($c) {
            static $logger = null;
            if ($logger === null) {
                $logger = LoggerFactory::create();
            }
            return $logger;
        });

        $container->set(PostApiController::class, function($c) {
            return new PostApiController(
                $c->get(PostRepositoryInterface::class),
                $c->get(Jwt::class),
                $c->get(LoggerInterface::class)
            );
        });

        $container->set(UserApiController::class, function($c) {
            return new UserApiController(
                $c->get(UserRepositoryInterface::class),
                $c->get(Jwt::class),
                $c->get(LoggerInterface::class)
            );
        });
    }

}