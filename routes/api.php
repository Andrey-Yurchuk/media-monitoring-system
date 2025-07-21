<?php

declare(strict_types=1);

use App\Infrastructure\Http\Router;
use App\Infrastructure\Http\NewsController;
use App\Infrastructure\Http\ReportController;

return function (Router $router, NewsController $newsController, ReportController $reportController): void {
    $router->addRoute('POST', '/api/v1/news', [$newsController, 'addNews']);
    $router->addRoute('GET', '/api/v1/news', [$newsController, 'getNewsList']);
    $router->addRoute('POST', '/api/v1/reports', [$reportController, 'generateReport']);
};