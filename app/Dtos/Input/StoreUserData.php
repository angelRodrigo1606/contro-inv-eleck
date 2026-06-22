<?php

namespace App\Dtos\Input;

readonly class StoreUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public string $password,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            email: $validated['email'],
            role: $validated['role'],
            password: $validated['password'],
        );
    }
}
