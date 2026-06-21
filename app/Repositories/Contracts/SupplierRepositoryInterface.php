<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface extends RepositoryInterface
{
    public function allOrdered(): Collection;

    public function paginateWithProductCount(int $perPage = 15): LengthAwarePaginator;

    public function hasProducts(Supplier $supplier): bool;
}
