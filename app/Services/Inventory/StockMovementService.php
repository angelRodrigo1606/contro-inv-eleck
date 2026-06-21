<?php

namespace App\Services\Inventory;

use App\Models\StockMovement;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Services\Exceptions\InsufficientStockException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository
    ) {}

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->stockMovementRepository->search($filters);
    }

    /**
     * @throws InsufficientStockException
     */
    public function register(array $data, User $user): StockMovement
    {
        $product = $this->productRepository->findOrFail($data['product_id']);

        if ($data['type'] === 'exit' && $product->quantity < $data['quantity']) {
            throw new InsufficientStockException;
        }

        return DB::transaction(function () use ($product, $data, $user) {
            if ($data['type'] === 'entry') {
                $this->productRepository->incrementQuantity($product, $data['quantity']);
            } else {
                $this->productRepository->decrementQuantity($product, $data['quantity']);
            }

            return $this->stockMovementRepository->createForProduct($product, [
                'user_id' => $user->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
