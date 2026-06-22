<?php

namespace App\Services\Catalog;

use App\Dtos\Data\SupplierData;
use App\Dtos\Input\StoreSupplierData;
use App\Dtos\Input\UpdateSupplierData;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\Exceptions\DependencyException;

class SupplierService
{
    public function __construct(private SupplierRepositoryInterface $supplierRepository) {}

    public function create(StoreSupplierData $data): SupplierData
    {
        return $this->supplierRepository->create($data);
    }

    public function update(int|string $id, UpdateSupplierData $data): SupplierData
    {
        return $this->supplierRepository->update($id, $data);
    }

    /**
     * @throws DependencyException
     */
    public function delete(int|string $id): void
    {
        if ($this->supplierRepository->hasProducts($id)) {
            throw new DependencyException('el proveedor');
        }

        $this->supplierRepository->delete($id);
    }
}
