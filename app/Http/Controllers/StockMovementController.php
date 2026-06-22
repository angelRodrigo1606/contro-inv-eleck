<?php

namespace App\Http\Controllers;

use App\Dtos\Input\RegisterStockMovementData;
use App\Http\Requests\StoreStockMovementRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Exceptions\InsufficientStockException;
use App\Services\Inventory\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function __construct(
        private StockMovementService $movementService,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function index(Request $request): View
    {
        $movements = $this->movementService->search($request->only(['type', 'product_id', 'from', 'to']))->toPaginator();
        $products = $this->productRepository->allOrdered();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    public function create(): View
    {
        $products = $this->productRepository->allOrdered();

        return view('stock-movements.create', compact('products'));
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        try {
            $this->movementService->register(RegisterStockMovementData::fromRequest($request->validated()), $request->user()->id);
        } catch (InsufficientStockException $e) {
            return redirect()->route('stock-movements.create')
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimiento registrado correctamente.');
    }
}
