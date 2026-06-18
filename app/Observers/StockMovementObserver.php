<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\Services\Inventory\StockAlertService;

class StockMovementObserver
{
    public function __construct(private StockAlertService $alertService) {}

    public function created(StockMovement $movement): void
    {
        $this->alertService->syncForProduct($movement->product);
    }
}
