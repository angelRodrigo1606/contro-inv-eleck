<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\SupplierData;
use App\Dtos\Input\StoreSupplierData;
use App\Dtos\Input\UpdateSupplierData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface SupplierRepositoryInterface
{
    /**
     * @return Collection<int, SupplierData>
     */
    public function all(): Collection;

    public function find(int|string $id): ?SupplierData;

    public function findOrFail(int|string $id): SupplierData;

    public function create(StoreSupplierData $data): SupplierData;

    public function update(int|string $id, UpdateSupplierData $data): SupplierData;

    public function delete(int|string $id): void;

    public function paginateWithProductCount(int $perPage = 15): PaginatedData;

    /**
     * @return Collection<int, SupplierData>
     */
    public function allOrdered(): Collection;

    public function hasProducts(int|string $id): bool;

    public function countProducts(int|string $id): int;
}
