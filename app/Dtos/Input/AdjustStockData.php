<?php

namespace App\Dtos\Input;

readonly class AdjustStockData
{
    public function __construct(
        public int $quantity,
        public ?string $reason,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            quantity: (int) $validated['quantity'],
            reason: $validated['reason'] ?? null,
        );
    }
}
