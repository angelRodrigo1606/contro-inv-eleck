<?php

namespace App\Dtos\Input;

readonly class RegisterStockMovementData
{
    public function __construct(
        public int $productId,
        public string $type,
        public int $quantity,
        public ?string $reference,
        public ?string $notes,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            productId: (int) $validated['product_id'],
            type: $validated['type'],
            quantity: (int) $validated['quantity'],
            reference: $validated['reference'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
