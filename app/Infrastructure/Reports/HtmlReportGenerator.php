<?php

declare(strict_types=1);

namespace App\Infrastructure\Reports;

use App\Application\Services\ReportGeneratorInterface;
use RuntimeException;

class HtmlReportGenerator implements ReportGeneratorInterface
{
    public function __construct(
        private readonly string $reportsDir = '/var/www/html/reports'
    ) {
        if (!@mkdir($this->reportsDir, 0755, true) && !is_dir($this->reportsDir)) {
            throw new RuntimeException("Failed to create reports directory: {$this->reportsDir}");
        }
    }

    public function generate(array $newsList): string
    {
        $reportId = uniqid('', true);
        $filename = "report_{$reportId}.html";
        $filepath = $this->reportsDir . '/' . $filename;
        
        $html = $this->generateHtmlContent($newsList);
        
        if (file_put_contents($filepath, $html) === false) {
            throw new RuntimeException("Failed to write report file: {$filepath}");
        }

        return '/reports/' . $filename;
    }

    private function generateHtmlContent(array $newsList): string
    {
        $html = '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="en">' . PHP_EOL;
        $html .= '<head>' . PHP_EOL;
        $html .= '    <meta charset="UTF-8">' . PHP_EOL;
        $html .= '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        $html .= '    <title>News Report</title>' . PHP_EOL;
        $html .= '    <style>' . PHP_EOL;
        $html .= '        body { font-family: Arial, sans-serif; margin: 20px; }' . PHP_EOL;
        $html .= '        h1 { color: #333; }' . PHP_EOL;
        $html .= '        ul { list-style-type: none; padding: 0; }' . PHP_EOL;
        $html .= '        li { margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }' . PHP_EOL;
        $html .= '        a { color: #0066cc; text-decoration: none; }' . PHP_EOL;
        $html .= '        a:hover { text-decoration: underline; }' . PHP_EOL;
        $html .= '        .date { color: #666; font-size: 0.9em; }' . PHP_EOL;
        $html .= '    </style>' . PHP_EOL;
        $html .= '</head>' . PHP_EOL;
        $html .= '<body>' . PHP_EOL;
        $html .= '    <h1>News Report</h1>' . PHP_EOL;
        $html .= '    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>' . PHP_EOL;
        $html .= '    <ul>' . PHP_EOL;
        
        foreach ($newsList as $news) {
            $html .= '        <li>' . PHP_EOL;
            $html .= '            <a href="' . htmlspecialchars($news->url->value) . '" target="_blank">' . 
                     htmlspecialchars($news->title->value) . '</a>' . PHP_EOL;
            $html .= '            <div class="date">Date: ' . $news->date->format('Y-m-d H:i:s') . '</div>' . PHP_EOL;
            $html .= '        </li>' . PHP_EOL;
        }
        
        $html .= '    </ul>' . PHP_EOL;
        $html .= '</body>' . PHP_EOL;
        $html .= '</html>';
        
        return $html;
    }
} 