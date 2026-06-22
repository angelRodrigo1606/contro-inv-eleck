<?php

namespace App\Dtos\Data;

use DateTimeImmutable;
use Illuminate\Support\Collection;

readonly class ProductData
{
    /**
     * @param  Collection<int, StockMovementData>  $stockMovements
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $sku,
        public float $price,
        public int $quantity,
        public int $minStock,
        public bool $isLowStock,
        public ?CategoryData $category,
        public ?SupplierData $supplier,
        public Collection $stockMovements,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $deletedAt,
    ) {}
}
