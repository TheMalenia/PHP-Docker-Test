<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\User\LoginUserService;
use App\Application\User\RegisterUserService;
use App\Infrastructure\Auth\Jwt;
use App\Infrastructure\Auth\JwtInterface;
use App\Infrastructure\Database\PdoFactory;
use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Database\PdoPostRepository;
use App\Infrastructure\Database\PdoUserRepository;
use App\Infrastructure\Logger\LoggerFactory;
use App\Presentation\Controllers\Api\PostApiController;
use App\Presentation\Controllers\Api\UserApiController;
use App\Presentation\Routes\Router;
use Psr\Log\LoggerInterface;
use App\Application\Post\GetAllPostsService;
use App\Application\Post\GetPostByIdService;
use App\Application\Post\CreatePostService;

final class Services
{
    public static function register($container): void
    {
        $container->set(PdoFactory::class, function () {
            static $instance = null;
            if ($instance === null) {
                $instance = PdoFactory::createFromEnv();
            }
            return $instance;
        });

        $container->set(PostRepositoryInterface::class, function ($c) {
            return new PdoPostRepository($c->get(PdoFactory::class), $c->get(LoggerInterface::class));
        });

        $container->set(UserRepositoryInterface::class, function ($c) {
            return new PdoUserRepository($c->get(PdoFactory::class), $c->get(LoggerInterface::class));
        });

        // Register the Jwt implementation under the JwtInterface so callers
        // can depend on the interface and be mocked in tests.
        $container->set(JwtInterface::class, function ($c) {
            return new Jwt(getenv('JWT_SECRET') ?: 'dev-secret-change-me');
        });

        // PSR logger (singleton)
        $container->set(LoggerInterface::class, function ($c) {
            static $logger = null;
            if ($logger === null) {
                $logger = LoggerFactory::create();
            }
            return $logger;
        });

        $container->set(
            PostApiController::class,
            fn ($c) =>
        new PostApiController(
            $c->get(GetAllPostsService::class),
            $c->get(GetPostByIdService::class),
            $c->get(CreatePostService::class),
            $c->get(JwtInterface::class),
            $c->get(LoggerInterface::class)
        )
        );

        $container->set(UserApiController::class, function ($c) {
            return new UserApiController(
                $c->get(RegisterUserService::class),
                $c->get(LoginUserService::class),
                $c->get(LoggerInterface::class)
            );
        });

        $container->set(Router::class, function ($c) {
            $routesFile = __DIR__ . '/../../Presentation/Routes/routes.php';
            return new Router($routesFile, $c);
        });

        $container->set(GetAllPostsService::class, function ($c) {
            return new GetAllPostsService(
                $c->get(PostRepositoryInterface::class)
            );
        });

        $container->set(GetPostByIdService::class, function ($c) {
            return new GetPostByIdService(
                $c->get(PostRepositoryInterface::class)
            );
        });

        $container->set(CreatePostService::class, function ($c) {
            return new CreatePostService(
                $c->get(PostRepositoryInterface::class)
            );
        });

        $container->set(LoginUserService::class, function ($c) {
            return new LoginUserService(
                $c->get(UserRepositoryInterface::class),
                $c->get(JwtInterface::class)
            );
        });

        $container->set(RegisterUserService::class, function ($c) {
            return new RegisterUserService(
                $c->get(UserRepositoryInterface::class)
            );
        });

    }

}
