<?php
// public/index.php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../bootstrap/app.php';

use App\Presentation\Http\Request;
use App\Presentation\Routes\Router;

$router = $container->get(Router::class);

$request = new Request();
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $request);