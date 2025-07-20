<?php

declare(strict_types=1);

namespace App\Application\Services;

interface HtmlParserInterface
{
    public function extractTitle(string $html): string;
} 