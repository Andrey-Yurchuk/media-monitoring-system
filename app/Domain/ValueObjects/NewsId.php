<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class NewsId
{
    public function __construct(
        private readonly string $_value
    ) {
        if (empty($_value)) {
            throw new InvalidArgumentException('News ID cannot be empty');
        }
    }

    public string $value {
        get => $this->_value;
    }
} 