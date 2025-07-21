<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Services\NewsService;
use App\Application\DTO\GenerateReportRequestDTO;
use App\Infrastructure\Http\Traits\HttpRequestHandler;
use Exception;
use InvalidArgumentException;
use JsonException;

class ReportController
{
    use HttpRequestHandler;

    public function __construct(
        private readonly NewsService $newsService
    ) {
    }

    public function generateReport(): void
    {
        try {
            $input = $this->validateRequestAndGetInput('POST');
        } catch (JsonException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request format'], JSON_THROW_ON_ERROR);
            return;
        }
        
        try {
            $requestDTO = GenerateReportRequestDTO::fromArray($input);
            $reportDTO = $this->newsService->generateReport($requestDTO);
            
            http_response_code(201);
            echo json_encode($reportDTO->toArray(), JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request data'], JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error'], JSON_THROW_ON_ERROR);
        }
    }
} 