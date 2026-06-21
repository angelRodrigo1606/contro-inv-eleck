<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Inventory\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CategoryRepositoryInterface $categoryRepository,
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->search($request->only(['search', 'category_id']));
        $categories = $this->categoryRepository->allOrdered();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = $this->categoryRepository->allOrdered();
        $suppliers = $this->supplierRepository->allOrdered();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated(), $request->user());

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product = $this->productService->findWithRelations($product->id);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = $this->categoryRepository->allOrdered();
        $suppliers = $this->supplierRepository->allOrdered();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function adjust(AdjustStockRequest $request, Product $product): RedirectResponse
    {
        $movement = $this->productService->adjustStock(
            $product,
            (int) $request->validated()['quantity'],
            $request->validated()['reason'] ?? null,
            $request->user()
        );

        if ($movement === null) {
            return redirect()->route('products.show', $product)
                ->with('info', 'La cantidad no cambió.');
        }

        return redirect()->route('products.show', $product)
            ->with('success', 'Stock ajustado correctamente.');
    }
}
