<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;

    public function allOrdered(): Collection;

    public function findWithRelations(int|string $id): Product;

    public function lowStockCount(): int;

    public function sumTotalValue(): float;

    public function updateQuantity(Product $product, int $quantity): Product;

    public function incrementQuantity(Product $product, int $quantity): Product;

    public function decrementQuantity(Product $product, int $quantity): Product;
}
