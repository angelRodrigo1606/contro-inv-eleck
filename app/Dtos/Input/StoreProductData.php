<?php

namespace App\Dtos\Input;

readonly class StoreProductData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $sku,
        public int $categoryId,
        public int $supplierId,
        public float $price,
        public int $quantity,
        public int $minStock,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            sku: $validated['sku'],
            categoryId: (int) $validated['category_id'],
            supplierId: (int) $validated['supplier_id'],
            price: (float) $validated['price'],
            quantity: (int) $validated['quantity'],
            minStock: (int) $validated['min_stock'],
        );
    }
}
