<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockMovementRepositoryInterface extends RepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;

    public function getByType(array $filters, string $type, bool $paginate = true): LengthAwarePaginator|Collection;

    public function createForProduct(Product $product, array $data): StockMovement;

    public function recent(int $limit = 10): Collection;
}
