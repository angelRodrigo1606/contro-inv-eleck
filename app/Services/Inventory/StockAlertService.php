<?php

namespace App\Services\Inventory;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Database\Eloquent\Builder;

class StockAlertService
{
    public function unresolved(): Builder
    {
        return LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at');
    }

    public function resolve(LowStockAlert $alert): void
    {
        $alert->resolve();
    }

    public function syncForProduct(Product $product): void
    {
        $product->refresh();

        if ($product->quantity <= $product->min_stock) {
            $this->createAlert($product);

            return;
        }

        $this->resolveAlertsForProduct($product);
    }

    private function createAlert(Product $product): void
    {
        $exists = LowStockAlert::where('product_id', $product->id)
            ->unresolved()
            ->exists();

        if ($exists) {
            return;
        }

        LowStockAlert::create(['product_id' => $product->id]);

        $this->notifyAdmins($product);
    }

    private function resolveAlertsForProduct(Product $product): void
    {
        LowStockAlert::where('product_id', $product->id)
            ->unresolved()
            ->update(['resolved_at' => now()]);
    }

    private function notifyAdmins(Product $product): void
    {
        User::where('role', 'administrador')->get()
            ->each(fn (User $admin) => $admin->notify(new LowStockNotification($product)));
    }
}
