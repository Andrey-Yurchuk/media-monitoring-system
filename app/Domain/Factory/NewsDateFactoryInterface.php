<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\ValueObjects\NewsDate;

interface NewsDateFactoryInterface
{
    public function now(): NewsDate;
    
    public function fromString(string $date): NewsDate;
} 