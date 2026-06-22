<?php

namespace App\Http\Controllers;

use App\Dtos\Input\StoreSupplierData;
use App\Dtos\Input\UpdateSupplierData;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Catalog\SupplierService;
use App\Services\Exceptions\DependencyException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierService $supplierService,
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function index(): View
    {
        $suppliers = $this->supplierRepository->paginateWithProductCount()->toPaginator();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->create(StoreSupplierData::fromRequest($request->validated()));

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function show(int $id): View
    {
        $supplier = $this->supplierRepository->findOrFail($id);
        $productsCount = $this->supplierRepository->countProducts($id);

        return view('suppliers.show', compact('supplier', 'productsCount'));
    }

    public function edit(int $id): View
    {
        $supplier = $this->supplierRepository->findOrFail($id);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, int $id): RedirectResponse
    {
        $this->supplierService->update($id, UpdateSupplierData::fromRequest($request->validated()));

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->supplierService->delete($id);
        } catch (DependencyException $e) {
            return redirect()->route('suppliers.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
