<?php

declare(strict_types=1);

namespace App\Application\DTO;

use InvalidArgumentException;

class AddNewsRequestDTO
{
    public function __construct(
        public readonly string $url
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['url']) || !is_string($data['url'])) {
            throw new InvalidArgumentException('URL is required and must be a string');
        }

        if (empty(trim($data['url']))) {
            throw new InvalidArgumentException('URL cannot be empty');
        }

        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        return new self($data['url']);
    }
} 