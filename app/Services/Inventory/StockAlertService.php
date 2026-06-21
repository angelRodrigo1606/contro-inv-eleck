<?php

namespace App\Services\Inventory;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Notifications\LowStockNotification;
use App\Repositories\Contracts\LowStockAlertRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockAlertService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private LowStockAlertRepositoryInterface $lowStockAlertRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function unresolved(): LengthAwarePaginator
    {
        return $this->lowStockAlertRepository->paginateUnresolved();
    }

    public function resolve(LowStockAlert $alert): void
    {
        $this->lowStockAlertRepository->resolve($alert);
    }

    public function syncForProduct(Product $product): void
    {
        $product->refresh();

        if ($product->quantity <= $product->min_stock) {
            $this->createAlert($product);

            return;
        }

        $this->lowStockAlertRepository->resolveForProduct($product);
    }

    private function createAlert(Product $product): void
    {
        if ($this->lowStockAlertRepository->existsUnresolvedForProduct($product)) {
            return;
        }

        $this->lowStockAlertRepository->createForProduct($product);

        $this->notifyAdmins($product);
    }

    private function notifyAdmins(Product $product): void
    {
        $this->userRepository->getAdmins()
            ->each(fn ($admin) => $admin->notify(new LowStockNotification($product)));
    }
}
