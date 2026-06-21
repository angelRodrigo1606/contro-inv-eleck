<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return Product::with(['category', 'supplier'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
    }

    public function allOrdered(): Collection
    {
        return Product::orderBy('name')->get();
    }

    public function findWithRelations(int|string $id): Product
    {
        return Product::with(['category', 'supplier', 'stockMovements.user'])
            ->findOrFail($id);
    }

    public function lowStockCount(): int
    {
        return Product::lowStock()->count();
    }

    public function sumTotalValue(): float
    {
        return (float) Product::sum(DB::raw('quantity * price'));
    }

    public function updateQuantity(Product $product, int $quantity): Product
    {
        $product->update(['quantity' => $quantity]);

        return $product;
    }

    public function incrementQuantity(Product $product, int $quantity): Product
    {
        $product->increment('quantity', $quantity);

        return $product;
    }

    public function decrementQuantity(Product $product, int $quantity): Product
    {
        $product->decrement('quantity', $quantity);

        return $product;
    }
}
