<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repository\NewsRepository;
use App\Application\DTO\NewsDTO;

class GetNewsListUseCase
{
    public function __construct(
        private readonly NewsRepository $newsRepository
    ) {
    }

    /**
     * @return NewsDTO[]
     */
    public function execute(): array
    {
        $newsList = $this->newsRepository->findAll();
        
        return array_map(static function ($news) {
            return NewsDTO::fromNews($news);
        }, $newsList);
    }
} 