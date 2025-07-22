<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Repository\NewsRepository;
use App\Domain\Entity\News;
use App\Domain\ValueObjects\NewsId;
use App\Domain\ValueObjects\NewsUrl;
use App\Domain\ValueObjects\NewsTitle;
use App\Domain\Factory\NewsDateFactoryInterface;
use PDO;

class PostgreSQLNewsRepository implements NewsRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly NewsDateFactoryInterface $dateFactory
    ) {
    }

    public function save(News $news): void
    {
        $sql = "INSERT INTO news (id, url, title, created_at) VALUES (:id, :url, :title, :created_at)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $news->id->value,
            'url' => $news->url->value,
            'title' => $news->title->value,
            'created_at' => $news->date->format('Y-m-d H:i:s')
        ]);
    }

    public function findById(NewsId $id): ?News
    {
        $sql = "SELECT id, url, title, created_at FROM news WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id->value]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return $this->createNewsFromRow($row);
    }

    public function findAll(): array
    {
        $sql = "SELECT id, url, title, created_at FROM news ORDER BY created_at DESC";
        
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'createNewsFromRow'], $rows);
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT id, url, title, created_at FROM news WHERE id IN ($placeholders) ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_map(fn($id) => $id->value, $ids));
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'createNewsFromRow'], $rows);
    }

    private function createNewsFromRow(array $row): News
    {
        return new News(
            new NewsId($row['id']),
            new NewsUrl($row['url']),
            new NewsTitle($row['title']),
            $this->dateFactory->fromString($row['created_at'])
        );
    }
} 