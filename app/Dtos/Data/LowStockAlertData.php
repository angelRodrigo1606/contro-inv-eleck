<?php

namespace App\Dtos\Data;

use DateTimeImmutable;

readonly class LowStockAlertData
{
    public function __construct(
        public int $id,
        public ProductData $product,
        public ?DateTimeImmutable $resolvedAt,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public bool $wasRecentlyCreated = false,
    ) {}
}
