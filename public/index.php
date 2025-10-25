<?php
// public/index.php

// API-only: no session start

// Composer autoloader (PSR-4)
require_once __DIR__ . '/../vendor/autoload.php';

$routesFile = __DIR__ . '/../app/Routes/routes.php';

use App\Container\SimpleContainer;
use App\Container\Services;
use App\Routes\Router;
use App\Http\Request;

$container = new SimpleContainer();
Services::register($container);

$router = new Router($routesFile, $container);

$request = new Request();
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $request);
