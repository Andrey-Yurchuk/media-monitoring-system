<?php

declare(strict_types=1);

namespace App\Application\DTO;

use InvalidArgumentException;

class GenerateReportRequestDTO
{
    public function __construct(
        public readonly array $newsIds
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['news_ids']) || !is_array($data['news_ids'])) {
            throw new InvalidArgumentException('news_ids array is required');
        }

        if (empty($data['news_ids'])) {
            throw new InvalidArgumentException('news_ids array cannot be empty');
        }

        foreach ($data['news_ids'] as $id) {
            if (!is_string($id) || empty(trim($id))) {
                throw new InvalidArgumentException('All news_ids must be non-empty strings');
            }
        }

        return new self($data['news_ids']);
    }
} 