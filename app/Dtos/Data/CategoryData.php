<?php

namespace App\Dtos\Data;

use DateTimeImmutable;

readonly class CategoryData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public bool $isActive,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt,
    ) {}
}
