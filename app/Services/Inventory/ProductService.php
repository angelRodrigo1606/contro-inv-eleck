<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Product::with(['category', 'supplier'])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, function ($q, $categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('name');

        return $query->paginate(15)->withQueryString();
    }

    public function create(array $data, User $creator): Product
    {
        return DB::transaction(function () use ($data, $creator) {
            $product = Product::create($data);

            if ($product->quantity > 0) {
                $product->stockMovements()->create([
                    'user_id' => $creator->id,
                    'type' => 'entry',
                    'quantity' => $product->quantity,
                    'reference' => 'Inventario inicial',
                    'notes' => 'Stock inicial al crear el producto',
                ]);
            }

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function adjustStock(Product $product, int $newQuantity, ?string $reason, User $user): ?StockMovement
    {
        $difference = $newQuantity - $product->quantity;

        if ($difference === 0) {
            return null;
        }

        return DB::transaction(function () use ($product, $newQuantity, $difference, $reason, $user) {
            $product->update(['quantity' => $newQuantity]);

            return $product->stockMovements()->create([
                'user_id' => $user->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'reference' => 'Ajuste manual',
                'notes' => $reason,
            ]);
        });
    }
}
