<?php

namespace App\Http\Controllers;

use App\Dtos\Input\AdjustStockData;
use App\Dtos\Input\StoreProductData;
use App\Dtos\Input\UpdateProductData;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Exceptions\InsufficientStockException;
use App\Services\Inventory\ProductService;
use Illuminate\Database\QueryException;
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
        $products = $this->productService->search($request->only(['search', 'category_id']))->toPaginator();
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
        $this->productService->create(StoreProductData::fromRequest($request->validated()), $request->user()->id);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(int $id): View
    {
        $product = $this->productService->findWithRelations($id);

        return view('products.show', compact('product'));
    }

    public function edit(int $id): View
    {
        $product = $this->productService->findWithRelations($id);
        $categories = $this->categoryRepository->allOrdered();
        $suppliers = $this->supplierRepository->allOrdered();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $this->productService->update($id, UpdateProductData::fromRequest($request->validated()));

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->productService->delete($id);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function adjust(AdjustStockRequest $request, int $id): RedirectResponse
    {
        try {
            $movement = $this->productService->adjustStock(
                $id,
                AdjustStockData::fromRequest($request->validated()),
                $request->user()->id
            );
        } catch (InsufficientStockException $e) {
            return redirect()->route('products.show', $id)
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (QueryException $e) {
            return redirect()->route('products.show', $id)
                ->with('error', 'No se pudo ajustar el stock porque otro usuario lo modificó o la operación resultaría en stock negativo.')
                ->withInput();
        }

        if ($movement === null) {
            return redirect()->route('products.show', $id)
                ->with('info', 'La cantidad no cambió.');
        }

        return redirect()->route('products.show', $id)
            ->with('success', 'Stock ajustado correctamente.');
    }
}
