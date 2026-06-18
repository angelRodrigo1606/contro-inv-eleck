<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\Exceptions\InsufficientStockException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = StockMovement::with(['product.category', 'user'])
            ->when($filters['type'] ?? null, function ($q, $type) {
                $q->where('type', $type);
            })
            ->when($filters['product_id'] ?? null, function ($q, $productId) {
                $q->where('product_id', $productId);
            })
            ->when($filters['from'] ?? null, function ($q, $from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to'] ?? null, function ($q, $to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->orderByDesc('created_at');

        return $query->paginate(20)->withQueryString();
    }

    /**
     * @throws InsufficientStockException
     */
    public function register(array $data, User $user): StockMovement
    {
        $product = Product::findOrFail($data['product_id']);

        if ($data['type'] === 'exit' && $product->quantity < $data['quantity']) {
            throw new InsufficientStockException;
        }

        return DB::transaction(function () use ($product, $data, $user) {
            if ($data['type'] === 'entry') {
                $product->increment('quantity', $data['quantity']);
            } else {
                $product->decrement('quantity', $data['quantity']);
            }

            return $product->stockMovements()->create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
