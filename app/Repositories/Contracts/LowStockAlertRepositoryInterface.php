<?php

namespace App\Repositories\Contracts;

use App\Models\LowStockAlert;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LowStockAlertRepositoryInterface extends RepositoryInterface
{
    public function paginateUnresolved(int $perPage = 15): LengthAwarePaginator;

    public function recentUnresolved(int $limit = 5): Collection;

    public function resolve(LowStockAlert $alert): void;

    public function resolveForProduct(Product $product): void;

    public function existsUnresolvedForProduct(Product $product): bool;

    public function createForProduct(Product $product): LowStockAlert;
}
