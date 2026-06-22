<?php

namespace App\Dtos\Data;

use DateTimeImmutable;

readonly class StockMovementData
{
    public function __construct(
        public int $id,
        public string $type,
        public int $quantity,
        public ?string $reference,
        public ?string $notes,
        public ?ProductData $product,
        public ?UserData $user,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
    ) {}
}
