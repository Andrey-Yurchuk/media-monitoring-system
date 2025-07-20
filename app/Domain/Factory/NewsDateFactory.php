<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\ValueObjects\NewsDate;
use DateTimeImmutable;
use InvalidArgumentException;

class NewsDateFactory implements NewsDateFactoryInterface
{
    public function now(): NewsDate
    {
        return new NewsDate(new DateTimeImmutable());
    }

    public function fromString(string $date): NewsDate
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        
        if ($dateTime === false) {
            throw new InvalidArgumentException('Invalid date format. Expected Y-m-d H:i:s');
        }

        return new NewsDate($dateTime);
    }
} 