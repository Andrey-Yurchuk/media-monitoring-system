<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\News;

class NewsDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly string $title,
        public readonly string $date
    ) {
    }

    public static function fromNews(News $news): self
    {
        return new self(
            $news->id->value,
            $news->url->value,
            $news->title->value,
            $news->date->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->title,
            'date' => $this->date
        ];
    }
} 