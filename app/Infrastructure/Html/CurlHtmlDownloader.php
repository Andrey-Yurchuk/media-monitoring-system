<?php

declare(strict_types=1);

namespace App\Infrastructure\Html;

use App\Application\Services\HtmlDownloaderInterface;
use RuntimeException;

class CurlHtmlDownloader implements HtmlDownloaderInterface
{
    public function download(string $url): string
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MediaMonitoringSystem/1.0)',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("Failed to download HTML from {$url}: {$error}");
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new RuntimeException("HTTP error {$httpCode} when downloading from {$url}");
        }
        
        if (empty($html)) {
            throw new RuntimeException("Empty response from {$url}");
        }
        
        return $html;
    }
} 