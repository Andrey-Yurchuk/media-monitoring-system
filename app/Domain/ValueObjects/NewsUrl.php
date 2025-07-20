<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class NewsUrl
{
    public function __construct(
        private readonly string $_value
    ) {
        if (empty($_value)) {
            throw new InvalidArgumentException('News URL cannot be empty');
        }

        if (!filter_var($_value, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }
    }

    public string $value {
        get => $this->_value;
    }
} 