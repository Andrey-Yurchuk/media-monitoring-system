<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entity\News;

interface ReportGeneratorInterface
{
    /**
     * @param News[] $newsList
     */
    public function generate(array $newsList): string;
} 