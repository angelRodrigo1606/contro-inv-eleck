<?php

namespace App\Services\Catalog;

use App\Models\Supplier;
use App\Services\Exceptions\DependencyException;

class SupplierService
{
    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier;
    }

    /**
     * @throws DependencyException
     */
    public function delete(Supplier $supplier): void
    {
        if ($supplier->products()->exists()) {
            throw new DependencyException('el proveedor');
        }

        $supplier->delete();
    }
}
