<?php

namespace App\Http\Controllers;

use App\Services\Inventory\StockAlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LowStockAlertController extends Controller
{
    public function __construct(private StockAlertService $alertService) {}

    public function index(): View
    {
        $alerts = $this->alertService->unresolved()->toPaginator();

        return view('low-stock-alerts.index', compact('alerts'));
    }

    public function resolve(int $id): RedirectResponse
    {
        $this->alertService->resolve($id);

        return redirect()->route('low-stock-alerts.index')
            ->with('success', 'Alerta marcada como resuelta.');
    }
}
