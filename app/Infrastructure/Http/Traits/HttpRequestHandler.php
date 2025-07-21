<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Traits;

trait HttpRequestHandler
{
    protected function validateRequestAndGetInput(string $expectedMethod): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed'], JSON_THROW_ON_ERROR);
            exit;
        }

        return json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function validateRequestMethod(string $expectedMethod): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed'], JSON_THROW_ON_ERROR);
            exit;
        }
    }
} 