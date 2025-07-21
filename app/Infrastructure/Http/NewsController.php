<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Services\NewsService;
use App\Application\DTO\AddNewsRequestDTO;
use App\Infrastructure\Http\Traits\HttpRequestHandler;
use Exception;
use InvalidArgumentException;
use JsonException;

class NewsController
{
    use HttpRequestHandler;

    public function __construct(
        private readonly NewsService $newsService
    ) {
    }

    public function addNews(): void
    {
        try {
            $input = $this->validateRequestAndGetInput('POST');
        } catch (JsonException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request format'], JSON_THROW_ON_ERROR);
            return;
        }
        
        try {
            $requestDTO = AddNewsRequestDTO::fromArray($input);
            $responseDTO = $this->newsService->addNews($requestDTO);
            
            http_response_code(201);
            echo json_encode($responseDTO->toArray(), JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request data'], JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error'], JSON_THROW_ON_ERROR);
        }
    }

    public function getNewsList(): void
    {
        $this->validateRequestMethod('GET');

        try {
            $newsDTOs = $this->newsService->getNewsList();
            
            $newsArray = array_map(static function ($newsDTO) {
                return $newsDTO->toArray();
            }, $newsDTOs);
            
            http_response_code(200);
            echo json_encode($newsArray, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error'], JSON_THROW_ON_ERROR);
        }
    }
} 