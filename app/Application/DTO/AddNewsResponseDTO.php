<?php

declare(strict_types=1);

namespace App\Application\DTO;

class AddNewsResponseDTO
{
    public function __construct(
        public readonly string $id
    ) {
    }

    public function toArray(): array
    {
        return ['id' => $this->id];
    }
} 