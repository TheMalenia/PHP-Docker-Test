<?php
// bootstrap/app.php

use App\Infrastructure\Container\Services;
use App\Infrastructure\Container\SimpleContainer;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new SimpleContainer();

Services::register($container);

return $container;
