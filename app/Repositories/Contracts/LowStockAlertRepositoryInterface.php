<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\LowStockAlertData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface LowStockAlertRepositoryInterface
{
    public function paginateUnresolved(int $perPage = 15): PaginatedData;

    /**
     * @return Collection<int, LowStockAlertData>
     */
    public function recentUnresolved(int $limit = 5): Collection;

    public function resolve(int|string $alertId): void;

    public function resolveForProduct(int|string $productId): void;

    public function existsUnresolvedForProduct(int|string $productId): bool;

    public function createForProduct(int|string $productId): LowStockAlertData;

    public function firstOrCreateUnresolvedForProduct(int|string $productId): LowStockAlertData;
}
