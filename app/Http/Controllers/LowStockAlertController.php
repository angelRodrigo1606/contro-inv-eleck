<?php

namespace App\Http\Controllers;

use App\Models\LowStockAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LowStockAlertController extends Controller
{
    public function index(): View
    {
        $alerts = LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('low-stock-alerts.index', compact('alerts'));
    }

    public function resolve(LowStockAlert $alert): RedirectResponse
    {
        $alert->resolve();

        return redirect()->route('low-stock-alerts.index')
            ->with('success', 'Alerta marcada como resuelta.');
    }
}
