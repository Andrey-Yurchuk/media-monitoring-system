<?php

declare(strict_types=1);

namespace App\Infrastructure\Html;

use App\Application\Services\HtmlParserInterface;
use DOMDocument;
use DOMXPath;

class DomHtmlParser implements HtmlParserInterface
{
    public function extractTitle(string $html): string
    {
        libxml_use_internal_errors(true);
        
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);

        $titleNodes = $xpath->query('//head/title');
        
        if ($titleNodes->length > 0) {
            $title = trim($titleNodes->item(0)->textContent);
            if (!empty($title)) {
                return $title;
            }
        }

        $titleNodes = $xpath->query('//title');
        
        if ($titleNodes->length > 0) {
            $title = trim($titleNodes->item(0)->textContent);
            if (!empty($title)) {
                return $title;
            }
        }

        $h1Nodes = $xpath->query('//h1');
        
        if ($h1Nodes->length > 0) {
            $title = trim($h1Nodes->item(0)->textContent);
            if (!empty($title)) {
                return $title;
            }
        }

        return 'Untitled News Article';
    }
} 