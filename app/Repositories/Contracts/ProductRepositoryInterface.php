<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\ProductData;
use App\Dtos\Input\StoreProductData;
use App\Dtos\Input\UpdateProductData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function search(array $filters): PaginatedData;

    /**
     * @return Collection<int, ProductData>
     */
    public function allOrdered(): Collection;

    public function findWithRelations(int|string $id): ProductData;

    public function findOrFail(int|string $id): ProductData;

    public function create(StoreProductData $data): ProductData;

    public function update(int|string $id, UpdateProductData $data): ProductData;

    public function delete(int|string $id): void;

    public function lowStockCount(): int;

    public function sumTotalValue(): float;

    public function updateQuantity(int|string $id, int $quantity): ProductData;

    public function incrementQuantity(int|string $id, int $quantity): ProductData;

    public function decrementQuantity(int|string $id, int $quantity): ProductData;
}
