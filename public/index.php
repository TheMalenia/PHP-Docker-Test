<?php
// public/index.php

// API-only: no session start

// Composer autoloader (PSR-4)
require_once __DIR__ . '/../vendor/autoload.php';

// Path to routes file (match directory case: Routes)
$routesFile = __DIR__ . '/../app/Routes/routes.php';

use App\Container\SimpleContainer;
use App\Container\Services;
use App\Routes\Router;
use App\Http\Request;

// -------------------------
// Bootstrap container & services
// -------------------------
$container = new SimpleContainer();
Services::register($container);

// -------------------------
// Setup routing
// -------------------------
$router = new Router($routesFile, $container);

// -------------------------
// Build and dispatch request
// -------------------------
$request = new Request();
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $request);
