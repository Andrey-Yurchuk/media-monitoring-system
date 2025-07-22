<?php

declare(strict_types=1);

namespace App\Infrastructure\Bootstrap;

use App\Infrastructure\Http\Router;
use Exception;

class Application
{
    public function __construct(
        private readonly Router $router
    ) {
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        try {
            $this->router->handleRequest();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }
} 