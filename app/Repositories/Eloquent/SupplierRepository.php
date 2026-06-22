<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\SupplierData;
use App\Dtos\Input\StoreSupplierData;
use App\Dtos\Input\UpdateSupplierData;
use App\Dtos\PaginatedData;
use App\Mappers\SupplierMapper;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Collection;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return SupplierMapper::toDataCollection($this->model->all());
    }

    public function find(int|string $id): ?SupplierData
    {
        $supplier = $this->doFind($id);

        return $supplier ? SupplierMapper::toData($supplier) : null;
    }

    public function findOrFail(int|string $id): SupplierData
    {
        return SupplierMapper::toData($this->doFindOrFail($id));
    }

    public function create(StoreSupplierData $data): SupplierData
    {
        $supplier = $this->doCreate([
            'name' => $data->name,
            'contact_name' => $data->contactName,
            'phone' => $data->phone,
            'address' => $data->address,
            'email' => $data->email,
            'is_active' => $data->isActive,
        ]);

        return SupplierMapper::toData($supplier);
    }

    public function update(int|string $id, UpdateSupplierData $data): SupplierData
    {
        $supplier = $this->doUpdate($id, [
            'name' => $data->name,
            'contact_name' => $data->contactName,
            'phone' => $data->phone,
            'address' => $data->address,
            'email' => $data->email,
            'is_active' => $data->isActive,
        ]);

        return SupplierMapper::toData($supplier);
    }

    public function delete(int|string $id): void
    {
        $this->doDelete($id);
    }

    public function paginateWithProductCount(int $perPage = 15): PaginatedData
    {
        $paginator = Supplier::orderBy('name')
            ->withCount('products')
            ->paginate($perPage);

        return PaginatedData::fromLengthAwarePaginator($paginator, [SupplierMapper::class, 'toData']);
    }

    public function allOrdered(): Collection
    {
        return SupplierMapper::toDataCollection(
            Supplier::orderBy('name')->get()
        );
    }

    public function hasProducts(int|string $id): bool
    {
        return $this->doFindOrFail($id)->products()->exists();
    }

    public function countProducts(int|string $id): int
    {
        return $this->doFindOrFail($id)->products()->count();
    }
}
