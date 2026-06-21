<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function allOrdered(): Collection
    {
        return Supplier::orderBy('name')->get();
    }

    public function paginateWithProductCount(int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::orderBy('name')
            ->withCount('products')
            ->paginate($perPage);
    }

    public function hasProducts(Supplier $supplier): bool
    {
        return $supplier->products()->exists();
    }
}
