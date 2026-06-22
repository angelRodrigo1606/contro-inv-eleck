<?php

namespace App\Dtos\Input;

readonly class UpdateProductData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $categoryId,
        public int $supplierId,
        public float $price,
        public int $minStock,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            categoryId: (int) $validated['category_id'],
            supplierId: (int) $validated['supplier_id'],
            price: (float) $validated['price'],
            minStock: (int) $validated['min_stock'],
        );
    }
}
