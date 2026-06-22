<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\UserData;
use App\Dtos\Input\StoreUserData;
use App\Dtos\Input\UpdateProfileData;
use App\Dtos\Input\UpdateUserData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function paginateOrdered(int $perPage = 15): PaginatedData;

    public function findOrFail(int|string $id): UserData;

    public function create(StoreUserData $data): UserData;

    public function update(int|string $id, UpdateUserData $data): UserData;

    public function updateProfile(int|string $id, UpdateProfileData $data): UserData;

    public function delete(int|string $id): void;

    /**
     * @return Collection<int, UserData>
     */
    public function getAdmins(): Collection;

    public function verifyPassword(int|string $id, string $password): bool;
}
