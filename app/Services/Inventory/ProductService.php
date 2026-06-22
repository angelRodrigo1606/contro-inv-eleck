<?php

namespace App\Services\Inventory;

use App\Dtos\Data\ProductData;
use App\Dtos\Data\StockMovementData;
use App\Dtos\Input\AdjustStockData;
use App\Dtos\Input\RegisterStockMovementData;
use App\Dtos\Input\StoreProductData;
use App\Dtos\Input\UpdateProductData;
use App\Dtos\PaginatedData;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository
    ) {}

    public function search(array $filters): PaginatedData
    {
        return $this->productRepository->search($filters);
    }

    public function findWithRelations(int|string $id): ProductData
    {
        return $this->productRepository->findWithRelations($id);
    }

    public function create(StoreProductData $data, int $creatorId): ProductData
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $product = $this->productRepository->create($data);

            if ($product->quantity > 0) {
                $this->stockMovementRepository->createForProduct($product->id, (new RegisterStockMovementData(
                    productId: $product->id,
                    type: 'entry',
                    quantity: $product->quantity,
                    reference: 'Inventario inicial',
                    notes: 'Stock inicial al crear el producto',
                ))->withUserId($creatorId));
            }

            return $product;
        });
    }

    public function update(int|string $id, UpdateProductData $data): ProductData
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int|string $id): void
    {
        $this->productRepository->delete($id);
    }

    public function adjustStock(int|string $productId, AdjustStockData $data, int $userId): ?StockMovementData
    {
        $product = $this->productRepository->findOrFail($productId);
        $difference = $data->quantity - $product->quantity;

        if ($difference === 0) {
            return null;
        }

        return DB::transaction(function () use ($productId, $data, $difference, $userId) {
            $this->productRepository->updateQuantity($productId, $data->quantity);

            return $this->stockMovementRepository->createForProduct($productId, (new RegisterStockMovementData(
                productId: $productId,
                type: 'adjustment',
                quantity: $difference,
                reference: 'Ajuste manual',
                notes: $data->reason,
            ))->withUserId($userId));
        });
    }
}
