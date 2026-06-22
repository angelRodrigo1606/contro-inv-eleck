<?php

namespace App\Dtos\Input;

readonly class UpdateProfileData
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            email: $validated['email'],
        );
    }
}
