<?php

namespace App\Presentation\Routes;

use Psr\Container\ContainerInterface;
use RuntimeException;

class Router
{
    private array $routes;
    private ?ContainerInterface $container;

    public function __construct(string $routesFile, ?ContainerInterface $container = null)
    {
        if (!file_exists($routesFile)) {
            throw new RuntimeException("Routes file not found: $routesFile");
        }

        $this->routes = require $routesFile;
        if (!is_array($this->routes)) {
            throw new RuntimeException("Routes file must return an array.");
        }
        $this->container = $container;
    }

    public function dispatch(string $requestUri, string $requestMethod, ?\App\Presentation\Http\Request $request = null): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';
        $requestMethod = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            [$method, $pattern, $handler] = $route;
            if (strtoupper($method) !== $requestMethod) {
                continue;
            }

            $regex = $this->patternToRegex($pattern);
            if (preg_match($regex, $path, $matches)) {
                // remove the full match
                array_shift($matches);
                // $matches are positional params, e.g. ['5']
                [$controllerName, $methodName] = $handler;

                // controller is provided as fully-qualified class name
                $fqcn = $controllerName;
                if (!class_exists($fqcn)) {
                    http_response_code(500);
                    echo "Controller class not found: $fqcn";
                    return;
                }

                if ($this->container && $this->container->has($fqcn)) {
                    $controller = $this->container->get($fqcn);
                } elseif ($this->container && method_exists($this->container, 'get')) {
                    // try to resolve generically if container supports get
                    try {
                        $controller = $this->container->get($fqcn);
                    } catch (\Throwable $e) {
                        // fallback
                        $controller = new $fqcn();
                    }
                } else {
                    $controller = new $fqcn();
                }

                if (!method_exists($controller, $methodName)) {
                    http_response_code(500);
                    echo "Controller method not found: {$controllerName}::{$methodName}";
                    return;
                }

                // call the controller method with extracted params
                // If controller expects Request as first parameter, pass it
                $reflection = new \ReflectionMethod($controller, $methodName);
                $params = $matches;
                $firstParam = $reflection->getParameters()[0] ?? null;
                if ($firstParam && $firstParam->getType() && $firstParam->getType()->getName() === \App\Presentation\Http\Request::class) {
                    array_unshift($params, $request);
                }
                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        // no route matched
        http_response_code(404);
        echo '404 - Not Found';
    }

    /**
     * Convert pattern like /post/{id} to regex #^/post/([^/]+)$#
     */
    private function patternToRegex(string $pattern): string
    {
        if ($pattern === '' || $pattern === '/') {
            return '#^/$#';
        }

        $regex = preg_replace('#\{[^/}]+\}#', '([^/]+)', $pattern);
        $regex = '#^' . rtrim($regex, '/') . '/?$#';
        return $regex;
    }
}
