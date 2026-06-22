<?php

namespace App\Services\Inventory;

use App\Dtos\Data\ProductData;
use App\Dtos\PaginatedData;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Repositories\Contracts\LowStockAlertRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class StockAlertService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LowStockAlertRepositoryInterface $lowStockAlertRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function unresolved(): PaginatedData
    {
        return $this->lowStockAlertRepository->paginateUnresolved();
    }

    public function resolve(int|string $alertId): void
    {
        $this->lowStockAlertRepository->resolve($alertId);
    }

    public function syncForProduct(int|string $productId): void
    {
        $product = $this->productRepository->findOrFail($productId);

        if ($product->quantity <= $product->minStock) {
            $this->createAlert($product);

            return;
        }

        $this->lowStockAlertRepository->resolveForProduct($productId);
    }

    private function createAlert(ProductData $product): void
    {
        $alert = $this->lowStockAlertRepository->firstOrCreateUnresolvedForProduct($product->id);

        if ($alert->wasRecentlyCreated) {
            $this->notifyAdmins($product);
        }
    }

    private function notifyAdmins(ProductData $product): void
    {
        $this->userRepository->getAdmins()
            ->each(fn (User $admin) => $admin->notify(new LowStockNotification($product)));
    }
}
