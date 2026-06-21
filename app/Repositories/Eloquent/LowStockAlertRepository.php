<?php

namespace App\Repositories\Eloquent;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Repositories\Contracts\LowStockAlertRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LowStockAlertRepository extends BaseRepository implements LowStockAlertRepositoryInterface
{
    public function __construct(LowStockAlert $model)
    {
        parent::__construct($model);
    }

    public function paginateUnresolved(int $perPage = 15): LengthAwarePaginator
    {
        return LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function recentUnresolved(int $limit = 5): Collection
    {
        return LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function resolve(LowStockAlert $alert): void
    {
        $alert->resolve();
    }

    public function resolveForProduct(Product $product): void
    {
        LowStockAlert::where('product_id', $product->id)
            ->unresolved()
            ->update(['resolved_at' => now()]);
    }

    public function existsUnresolvedForProduct(Product $product): bool
    {
        return LowStockAlert::where('product_id', $product->id)
            ->unresolved()
            ->exists();
    }

    public function createForProduct(Product $product): LowStockAlert
    {
        return LowStockAlert::create(['product_id' => $product->id]);
    }
}
