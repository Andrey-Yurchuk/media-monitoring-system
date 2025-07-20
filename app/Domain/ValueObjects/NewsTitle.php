<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class NewsTitle
{
    public function __construct(
        private readonly string $_value
    ) {
        if (empty(trim($_value))) {
            throw new InvalidArgumentException('News title cannot be empty');
        }

        if (mb_strlen($_value) > 500) {
            throw new InvalidArgumentException('News title cannot be longer than 500 characters');
        }
    }

    public string $value {
        get => $this->_value;
    }
} 