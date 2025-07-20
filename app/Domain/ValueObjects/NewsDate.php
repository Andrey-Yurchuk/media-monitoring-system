<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use DateTimeImmutable;

class NewsDate
{
    public function __construct(
        private readonly DateTimeImmutable $_value
    ) {
    }

    public DateTimeImmutable $value {
        get => $this->_value;
    }

    public function format(string $format): string
    {
        return $this->_value->format($format);
    }
} 