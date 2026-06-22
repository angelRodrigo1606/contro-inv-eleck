<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\ProductData;
use App\Dtos\Input\StoreProductData;
use App\Dtos\Input\UpdateProductData;
use App\Dtos\PaginatedData;
use App\Mappers\ProductMapper;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function search(array $filters): PaginatedData
    {
        $paginator = Product::with(['category', 'supplier'])
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

        return PaginatedData::fromLengthAwarePaginator($paginator, [ProductMapper::class, 'toData']);
    }

    public function allOrdered(): Collection
    {
        return ProductMapper::toDataCollection(
            Product::orderBy('name')->get()
        );
    }

    public function findWithRelations(int|string $id): ProductData
    {
        $product = Product::with(['category', 'supplier', 'stockMovements.user'])
            ->findOrFail($id);

        return ProductMapper::toData($product);
    }

    public function findOrFail(int|string $id): ProductData
    {
        return ProductMapper::toData($this->doFindOrFail($id));
    }

    public function create(StoreProductData $data): ProductData
    {
        $product = $this->doCreate([
            'name' => $data->name,
            'description' => $data->description,
            'sku' => $data->sku,
            'category_id' => $data->categoryId,
            'supplier_id' => $data->supplierId,
            'price' => $data->price,
            'quantity' => $data->quantity,
            'min_stock' => $data->minStock,
        ]);

        return ProductMapper::toData($product);
    }

    public function update(int|string $id, UpdateProductData $data): ProductData
    {
        $product = $this->doUpdate($id, [
            'name' => $data->name,
            'description' => $data->description,
            'category_id' => $data->categoryId,
            'supplier_id' => $data->supplierId,
            'price' => $data->price,
            'min_stock' => $data->minStock,
        ]);

        return ProductMapper::toData($product);
    }

    public function delete(int|string $id): void
    {
        $this->doDelete($id);
    }

    public function lowStockCount(): int
    {
        return Product::lowStock()->count();
    }

    public function sumTotalValue(): float
    {
        return (float) Product::sum(DB::raw('quantity * price'));
    }

    public function updateQuantity(int|string $id, int $quantity): ProductData
    {
        $product = $this->doFindOrFail($id);
        $product->update(['quantity' => $quantity]);

        return ProductMapper::toData($product);
    }

    public function incrementQuantity(int|string $id, int $quantity): ProductData
    {
        $product = $this->doFindOrFail($id);
        $product->increment('quantity', $quantity);

        return ProductMapper::toData($product);
    }

    public function decrementQuantity(int|string $id, int $quantity): ProductData
    {
        $product = $this->doFindOrFail($id);
        $product->decrement('quantity', $quantity);

        return ProductMapper::toData($product);
    }
}
