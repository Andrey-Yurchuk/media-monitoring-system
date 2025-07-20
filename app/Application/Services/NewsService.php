<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\UseCases\AddNewsUseCase;
use App\Application\UseCases\GetNewsListUseCase;
use App\Application\UseCases\GenerateReportUseCase;
use App\Application\DTO\AddNewsRequestDTO;
use App\Application\DTO\AddNewsResponseDTO;
use App\Application\DTO\NewsDTO;
use App\Application\DTO\GenerateReportRequestDTO;
use App\Application\DTO\ReportDTO;

class NewsService
{
    public function __construct(
        private readonly AddNewsUseCase $addNewsUseCase,
        private readonly GetNewsListUseCase $getNewsListUseCase,
        private readonly GenerateReportUseCase $generateReportUseCase
    ) {
    }

    public function addNews(AddNewsRequestDTO $request): AddNewsResponseDTO
    {
        return $this->addNewsUseCase->execute($request);
    }

    /**
     * @return NewsDTO[]
     */
    public function getNewsList(): array
    {
        return $this->getNewsListUseCase->execute();
    }

    public function generateReport(GenerateReportRequestDTO $request): ReportDTO
    {
        return $this->generateReportUseCase->execute($request);
    }
} 