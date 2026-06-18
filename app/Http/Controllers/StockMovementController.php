<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockMovement::with(['product.category', 'user'])
            ->when($request->type, function ($q, $type) {
                $q->where('type', $type);
            })
            ->when($request->product_id, function ($q, $productId) {
                $q->where('product_id', $productId);
            })
            ->when($request->from, function ($q, $from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($request->to, function ($q, $to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->orderByDesc('created_at');

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();

        return view('stock-movements.create', compact('products'));
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        if ($validated['type'] === 'exit' && $product->quantity < $validated['quantity']) {
            return redirect()->route('stock-movements.create')
                ->with('error', 'Stock insuficiente para registrar la salida.')
                ->withInput();
        }

        DB::transaction(function () use ($product, $validated, $request) {
            if ($validated['type'] === 'entry') {
                $product->increment('quantity', $validated['quantity']);
            } else {
                $product->decrement('quantity', $validated['quantity']);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }
}
