<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\ValueObjects\NewsId;
use App\Domain\Repository\NewsRepository;
use App\Application\Services\ReportGeneratorInterface;
use App\Application\DTO\GenerateReportRequestDTO;
use App\Application\DTO\ReportDTO;
use InvalidArgumentException;

class GenerateReportUseCase
{
    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly ReportGeneratorInterface $reportGenerator
    ) {
    }

    public function execute(GenerateReportRequestDTO $request): ReportDTO
    {
        $newsIdObjects = array_map(static function ($id) {
            return new NewsId($id);
        }, $request->newsIds);

        $newsList = $this->newsRepository->findByIds($newsIdObjects);
        
        if (empty($newsList)) {
            throw new InvalidArgumentException('No news found with provided IDs');
        }

        $reportPath = $this->reportGenerator->generate($newsList);
        
        return new ReportDTO($reportPath);
    }
} 