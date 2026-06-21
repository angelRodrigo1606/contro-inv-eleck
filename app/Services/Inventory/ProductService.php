<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository
    ) {}

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->productRepository->search($filters);
    }

    public function findWithRelations(int|string $id): Product
    {
        return $this->productRepository->findWithRelations($id);
    }

    public function create(array $data, User $creator): Product
    {
        return DB::transaction(function () use ($data, $creator) {
            $product = $this->productRepository->create($data);

            if ($product->quantity > 0) {
                $this->stockMovementRepository->createForProduct($product, [
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
        return $this->productRepository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }

    public function adjustStock(Product $product, int $newQuantity, ?string $reason, User $user): ?StockMovement
    {
        $difference = $newQuantity - $product->quantity;

        if ($difference === 0) {
            return null;
        }

        return DB::transaction(function () use ($product, $newQuantity, $difference, $reason, $user) {
            $this->productRepository->updateQuantity($product, $newQuantity);

            return $this->stockMovementRepository->createForProduct($product, [
                'user_id' => $user->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'reference' => 'Ajuste manual',
                'notes' => $reason,
            ]);
        });
    }
}
