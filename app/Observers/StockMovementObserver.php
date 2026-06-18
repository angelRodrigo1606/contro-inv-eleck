<?php

namespace App\Observers;

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Notifications\LowStockNotification;

class StockMovementObserver
{
    public function created(StockMovement $movement): void
    {
        $this->checkAlerts($movement->product);
    }

    private function checkAlerts(Product $product): void
    {
        $product->refresh();

        if ($product->quantity <= $product->min_stock) {
            $this->createAlert($product);
        } else {
            $this->resolveAlert($product);
        }
    }

    private function createAlert(Product $product): void
    {
        $existing = LowStockAlert::where('product_id', $product->id)
            ->whereNull('resolved_at')
            ->first();

        if ($existing) {
            return;
        }

        LowStockAlert::create(['product_id' => $product->id]);

        $admins = User::where('role', 'administrador')->get();
        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($product));
        }
    }

    private function resolveAlert(Product $product): void
    {
        LowStockAlert::where('product_id', $product->id)
            ->whereNull('resolved_at')
            ->update(['resolved_at' => now()]);
    }
}
