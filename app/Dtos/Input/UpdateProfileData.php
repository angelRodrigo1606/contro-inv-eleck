<?php

namespace App\Dtos\Input;

use Illuminate\Http\UploadedFile;

readonly class UpdateProfileData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?UploadedFile $avatar,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            email: $validated['email'],
            avatar: $validated['avatar'] ?? null,
        );
    }
}
