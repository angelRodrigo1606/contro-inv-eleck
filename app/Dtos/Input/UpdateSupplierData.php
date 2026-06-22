<?php

namespace App\Dtos\Input;

readonly class UpdateSupplierData
{
    public function __construct(
        public string $name,
        public ?string $contactName,
        public ?string $phone,
        public ?string $address,
        public ?string $email,
        public bool $isActive,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            contactName: $validated['contact_name'] ?? null,
            phone: $validated['phone'] ?? null,
            address: $validated['address'] ?? null,
            email: $validated['email'] ?? null,
            isActive: $validated['is_active'] ?? true,
        );
    }
}
