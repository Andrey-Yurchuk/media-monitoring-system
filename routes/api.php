<?php

declare(strict_types=1);

use App\Infrastructure\Http\Router;
use App\Infrastructure\Http\NewsController;
use App\Infrastructure\Http\ReportController;

return function (Router $router, NewsController $newsController, ReportController $reportController): void {
    // Health check endpoint
    $router->addRoute('GET', '/health', function() {
        http_response_code(200);
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.1',
            'deployment' => 'GitHub Actions'
        ], JSON_THROW_ON_ERROR);
    });
    
    // API routes
    $router->addRoute('POST', '/api/v1/news', [$newsController, 'addNews']);
    $router->addRoute('GET', '/api/v1/news', [$newsController, 'getNewsList']);
    $router->addRoute('POST', '/api/v1/reports', [$reportController, 'generateReport']);
};