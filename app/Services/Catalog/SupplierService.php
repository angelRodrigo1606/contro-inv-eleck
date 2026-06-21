<?php

namespace App\Services\Catalog;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Exceptions\DependencyException;

class SupplierService
{
    public function __construct(private SupplierRepositoryInterface $supplierRepository) {}

    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    /**
     * @throws DependencyException
     */
    public function delete(Supplier $supplier): void
    {
        if ($this->supplierRepository->hasProducts($supplier)) {
            throw new DependencyException('el proveedor');
        }

        $this->supplierRepository->delete($supplier);
    }
}
