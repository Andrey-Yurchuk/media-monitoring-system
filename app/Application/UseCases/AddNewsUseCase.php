<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\News;
use App\Domain\ValueObjects\NewsId;
use App\Domain\ValueObjects\NewsUrl;
use App\Domain\ValueObjects\NewsTitle;
use App\Domain\Repository\NewsRepository;
use App\Domain\Factory\NewsDateFactoryInterface;
use App\Application\Services\HtmlDownloaderInterface;
use App\Application\Services\HtmlParserInterface;
use App\Application\DTO\AddNewsRequestDTO;
use App\Application\DTO\AddNewsResponseDTO;

class AddNewsUseCase
{
    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly HtmlDownloaderInterface $htmlDownloader,
        private readonly HtmlParserInterface $htmlParser,
        private readonly NewsDateFactoryInterface $dateFactory
    ) {
    }

    public function execute(AddNewsRequestDTO $request): AddNewsResponseDTO
    {
        $newsUrl = new NewsUrl($request->url);

        $html = $this->htmlDownloader->download($newsUrl->value);

        $title = $this->htmlParser->extractTitle($html);
        $newsTitle = new NewsTitle($title);
        $newsDate = $this->dateFactory->now();
        $newsId = new NewsId(uniqid('', true));

        $news = new News($newsId, $newsUrl, $newsTitle, $newsDate);

        $this->newsRepository->save($news);
        
        return new AddNewsResponseDTO($newsId->value);
    }
} 