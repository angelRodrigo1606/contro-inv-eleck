<?php

namespace App\Dtos\Data;

use DateTimeImmutable;

readonly class UserData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role,
        public ?string $avatar,
        public ?DateTimeImmutable $emailVerifiedAt,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
    ) {}
}
