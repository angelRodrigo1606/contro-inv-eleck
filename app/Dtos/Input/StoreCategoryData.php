<?php

namespace App\Dtos\Input;

readonly class StoreCategoryData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public bool $isActive,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            isActive: $validated['is_active'] ?? true,
        );
    }
}
