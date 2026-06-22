<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\CategoryData;
use App\Dtos\Input\StoreCategoryData;
use App\Dtos\Input\UpdateCategoryData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection<int, CategoryData>
     */
    public function all(): Collection;

    public function find(int|string $id): ?CategoryData;

    public function findOrFail(int|string $id): CategoryData;

    public function create(StoreCategoryData $data): CategoryData;

    public function update(int|string $id, UpdateCategoryData $data): CategoryData;

    public function delete(int|string $id): void;

    public function paginateWithProductCount(int $perPage = 15): PaginatedData;

    /**
     * @return Collection<int, CategoryData>
     */
    public function allOrdered(): Collection;

    public function hasProducts(int|string $id): bool;

    public function countProducts(int|string $id): int;
}
