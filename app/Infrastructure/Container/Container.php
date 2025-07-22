<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Infrastructure\Database\PostgreSQLNewsRepository;
use App\Infrastructure\Html\CurlHtmlDownloader;
use App\Infrastructure\Html\DomHtmlParser;
use App\Infrastructure\Reports\HtmlReportGenerator;
use App\Infrastructure\Http\Router;
use App\Infrastructure\Http\NewsController;
use App\Infrastructure\Http\ReportController;
use App\Infrastructure\Bootstrap\Application;
use App\Application\UseCases\AddNewsUseCase;
use App\Application\UseCases\GetNewsListUseCase;
use App\Application\UseCases\GenerateReportUseCase;
use App\Application\Services\NewsService;
use App\Domain\Factory\NewsDateFactory;
use InvalidArgumentException;
use PDO;

class Container
{
    private array $services = [];

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            $this->services[$id] = $this->create($id);
        }
        return $this->services[$id];
    }

    private function create(string $id): mixed
    {
        return match ($id) {
            'pdo' => $this->createPdo(),
            'dateFactory' => $this->createDateFactory(),
            'newsRepository' => $this->createNewsRepository(),
            'htmlDownloader' => $this->createHtmlDownloader(),
            'htmlParser' => $this->createHtmlParser(),
            'reportGenerator' => $this->createReportGenerator(),
            'addNewsUseCase' => $this->createAddNewsUseCase(),
            'getNewsListUseCase' => $this->createGetNewsListUseCase(),
            'generateReportUseCase' => $this->createGenerateReportUseCase(),
            'newsService' => $this->createNewsService(),
            'newsController' => $this->createNewsController(),
            'reportController' => $this->createReportController(),
            'router' => $this->createRouter(),
            'app' => $this->createApp(),
            default => throw new InvalidArgumentException("Service '{$id}' not found")
        };
    }

    private function createPdo(): PDO
    {
        $pdo = new PDO(
            'pgsql:host=' . $_ENV['DB_HOST'] . 
            ';port=' . $_ENV['DB_PORT'] . 
            ';dbname=' . $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    private function createDateFactory(): NewsDateFactory
    {
        return new NewsDateFactory();
    }

    private function createNewsRepository(): PostgreSQLNewsRepository
    {
        return new PostgreSQLNewsRepository($this->get('pdo'), $this->get('dateFactory'));
    }

    private function createHtmlDownloader(): CurlHtmlDownloader
    {
        return new CurlHtmlDownloader();
    }

    private function createHtmlParser(): DomHtmlParser
    {
        return new DomHtmlParser();
    }

    private function createReportGenerator(): HtmlReportGenerator
    {
        return new HtmlReportGenerator();
    }

    private function createAddNewsUseCase(): AddNewsUseCase
    {
        return new AddNewsUseCase(
            $this->get('newsRepository'),
            $this->get('htmlDownloader'),
            $this->get('htmlParser'),
            $this->get('dateFactory')
        );
    }

    private function createGetNewsListUseCase(): GetNewsListUseCase
    {
        return new GetNewsListUseCase($this->get('newsRepository'));
    }

    private function createGenerateReportUseCase(): GenerateReportUseCase
    {
        return new GenerateReportUseCase(
            $this->get('newsRepository'),
            $this->get('reportGenerator')
        );
    }

    private function createNewsService(): NewsService
    {
        return new NewsService(
            $this->get('addNewsUseCase'),
            $this->get('getNewsListUseCase'),
            $this->get('generateReportUseCase')
        );
    }

    private function createNewsController(): NewsController
    {
        return new NewsController($this->get('newsService'));
    }

    private function createReportController(): ReportController
    {
        return new ReportController($this->get('newsService'));
    }

    private function createRouter(): Router
    {
        $router = new Router();
        $newsController = $this->get('newsController');
        $reportController = $this->get('reportController');
        $routesLoader = require __DIR__ . '/../../../routes/api.php';
        $routesLoader($router, $newsController, $reportController);
        
        return $router;
    }

    private function createApp(): Application
    {
        return new Application($this->get('router'));
    }
} 