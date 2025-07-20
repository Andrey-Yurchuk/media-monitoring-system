<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\News;
use App\Domain\ValueObjects\NewsId;

interface NewsRepository
{
    public function save(News $news): void;
    
    public function findById(NewsId $id): ?News;
    
    public function findAll(): array;
    
    public function findByIds(array $ids): array;
} 