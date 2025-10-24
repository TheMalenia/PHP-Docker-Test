<?php
// tests/router_test.php - lightweight smoke test for Router dispatch
require_once __DIR__ . '/../vendor/autoload.php';

$routesFile = __DIR__ . '/../app/routes/routes.php';
$router = new App\Routes\Router($routesFile);

$tests = [
    ['uri' => '/', 'method' => 'GET'],
    ['uri' => '/post', 'method' => 'GET'],
    ['uri' => '/post/1', 'method' => 'GET'],
    ['uri' => '/create_post', 'method' => 'GET'],
    ['uri' => '/login', 'method' => 'GET'],
];

foreach ($tests as $t) {
    ob_start();
    // simulate server vars
    $uri = $t['uri'];
    $method = $t['method'];
    echo "\n--- Testing: $method $uri ---\n";
    $router->dispatch($uri, $method);
    $output = ob_get_clean();
    echo $output;
}

echo "\nTests complete.\n";
