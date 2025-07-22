<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Container\Container;

try {
    $container = new Container();
    $app = $container->get('app');
    $app->handle();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()], JSON_THROW_ON_ERROR);
} 