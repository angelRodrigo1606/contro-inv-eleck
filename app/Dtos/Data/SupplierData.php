<?php

namespace App\Dtos\Data;

use DateTimeImmutable;

readonly class SupplierData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $contactName,
        public ?string $phone,
        public ?string $address,
        public ?string $email,
        public bool $isActive,
        public int $productsCount,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt,
    ) {}
}
