<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\LowStockAlertData;
use App\Dtos\PaginatedData;
use App\Mappers\LowStockAlertMapper;
use App\Models\LowStockAlert;
use App\Repositories\Contracts\LowStockAlertRepositoryInterface;
use Illuminate\Support\Collection;

class LowStockAlertRepository extends BaseRepository implements LowStockAlertRepositoryInterface
{
    public function __construct(LowStockAlert $model)
    {
        parent::__construct($model);
    }

    public function paginateUnresolved(int $perPage = 15): PaginatedData
    {
        $paginator = LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return PaginatedData::fromLengthAwarePaginator($paginator, [LowStockAlertMapper::class, 'toData']);
    }

    public function recentUnresolved(int $limit = 5): Collection
    {
        return LowStockAlertMapper::toDataCollection(
            LowStockAlert::with('product')
                ->unresolved()
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
        );
    }

    public function resolve(int|string $alertId): void
    {
        $this->doFindOrFail($alertId)->resolve();
    }

    public function resolveForProduct(int|string $productId): void
    {
        LowStockAlert::where('product_id', $productId)
            ->unresolved()
            ->update(['resolved_at' => now()]);
    }

    public function existsUnresolvedForProduct(int|string $productId): bool
    {
        return LowStockAlert::where('product_id', $productId)
            ->unresolved()
            ->exists();
    }

    public function createForProduct(int|string $productId): LowStockAlertData
    {
        $alert = LowStockAlert::create(['product_id' => $productId]);

        return LowStockAlertMapper::toData($alert);
    }

    public function firstOrCreateUnresolvedForProduct(int|string $productId): LowStockAlertData
    {
        $alert = LowStockAlert::firstOrCreate(
            ['product_id' => $productId, 'resolved_at' => null],
            ['product_id' => $productId]
        );

        return LowStockAlertMapper::toData($alert);
    }
}
