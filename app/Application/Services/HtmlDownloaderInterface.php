<?php

declare(strict_types=1);

namespace App\Application\Services;

interface HtmlDownloaderInterface
{
    public function download(string $url): string;
} 