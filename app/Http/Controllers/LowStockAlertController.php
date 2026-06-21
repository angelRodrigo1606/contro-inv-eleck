<?php

namespace App\Http\Controllers;

use App\Models\LowStockAlert;
use App\Services\Inventory\StockAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LowStockAlertController extends Controller
{
    public function __construct(private StockAlertService $alertService) {}

    public function index(): View
    {
        $alerts = $this->alertService->unresolved();

        return view('low-stock-alerts.index', compact('alerts'));
    }

    public function resolve(LowStockAlert $alert): RedirectResponse
    {
        $this->alertService->resolve($alert);

        return redirect()->route('low-stock-alerts.index')
            ->with('success', 'Alerta marcada como resuelta.');
    }
}
