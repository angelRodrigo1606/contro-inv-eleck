<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
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
        $suppliers = $this->supplierRepository->paginateWithProductCount();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->supplierService->update($supplier, $request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $this->supplierService->delete($supplier);
        } catch (DependencyException $e) {
            return redirect()->route('suppliers.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
