<?php

namespace App\Repositories\Contracts;

use App\Dtos\Data\StockMovementData;
use App\Dtos\Input\RegisterStockMovementData;
use App\Dtos\PaginatedData;
use Illuminate\Support\Collection;

interface StockMovementRepositoryInterface
{
    public function search(array $filters): PaginatedData;

    public function getByType(array $filters, string $type, bool $paginate = true): PaginatedData|Collection;

    public function createForProduct(int|string $productId, RegisterStockMovementData $data): StockMovementData;

    /**
     * @return Collection<int, StockMovementData>
     */
    public function recent(int $limit = 10): Collection;
}
