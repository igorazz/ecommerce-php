<?php

use App\Core\HttpException;

$app = require __DIR__ . '/../app/bootstrap.php';
$router = $app['router'];

$routes = require __DIR__ . '/../app/front_routes.php';

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$scriptBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$path = $uriPath ?: '/';

if ($scriptBase !== '' && $scriptBase !== '/' && str_starts_with($path, $scriptBase)) {
    $path = substr($path, strlen($scriptBase));
}

$path = $path === '' ? '/' : '/' . ltrim($path, '/');
$path = rawurldecode($path);

if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$route = $routes[$path][$method] ?? null;
if (!$route) {
    throw new HttpException('Rota não encontrada.', 404);
}

$router->dispatch($route);
