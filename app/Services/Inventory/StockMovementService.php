<?php

namespace App\Services\Inventory;

use App\Dtos\Data\StockMovementData;
use App\Dtos\Input\RegisterStockMovementData;
use App\Dtos\PaginatedData;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Services\Exceptions\InsufficientStockException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository
    ) {}

    public function search(array $filters): PaginatedData
    {
        return $this->stockMovementRepository->search($filters);
    }

    /**
     * @throws InsufficientStockException
     */
    public function register(RegisterStockMovementData $data, int $userId): StockMovementData
    {
        $product = $this->productRepository->findOrFail($data->productId);

        if ($data->type === 'exit' && $product->quantity < $data->quantity) {
            throw new InsufficientStockException;
        }

        return DB::transaction(function () use ($product, $data, $userId) {
            if ($data->type === 'entry') {
                $this->productRepository->incrementQuantity($product->id, $data->quantity);
            } else {
                $this->productRepository->decrementQuantity($product->id, $data->quantity);
            }

            return $this->stockMovementRepository->createForProduct($product->id, $data->withUserId($userId));
        });
    }

    public function getByType(array $filters, string $type, bool $paginate = true): PaginatedData|Collection
    {
        return $this->stockMovementRepository->getByType($filters, $type, $paginate);
    }
}
