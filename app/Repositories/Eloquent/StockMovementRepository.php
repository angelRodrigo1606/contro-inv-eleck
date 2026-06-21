<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return StockMovement::with(['product.category', 'user'])
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['product_id'] ?? null, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($filters['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function getByType(array $filters, string $type, bool $paginate = true): LengthAwarePaginator|Collection
    {
        $query = StockMovement::with(['product.category', 'product.supplier', 'user'])
            ->where('type', $type)
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['product_id'] ?? null, fn ($query, $id) => $query->where('product_id', $id))
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->whereHas('product', fn ($subQuery) => $subQuery->where('category_id', $categoryId));
            })
            ->when($filters['supplier_id'] ?? null, function ($query, $supplierId) {
                $query->whereHas('product', fn ($subQuery) => $subQuery->where('supplier_id', $supplierId));
            })
            ->orderByDesc('created_at');

        return $paginate
            ? $query->paginate(20)->withQueryString()
            : $query->get();
    }

    public function createForProduct(Product $product, array $data): StockMovement
    {
        return $product->stockMovements()->create($data);
    }

    public function recent(int $limit = 10): Collection
    {
        return StockMovement::with(['product', 'user'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
