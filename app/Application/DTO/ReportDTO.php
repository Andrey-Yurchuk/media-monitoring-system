<?php

declare(strict_types=1);

namespace App\Application\DTO;

class ReportDTO
{
    public function __construct(
        public readonly string $reportUrl
    ) {
    }

    public function toArray(): array
    {
        return ['report_url' => $this->reportUrl];
    }
} 