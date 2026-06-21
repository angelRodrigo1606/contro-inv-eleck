<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function allOrdered(): Collection;

    public function paginateWithProductCount(int $perPage = 15): LengthAwarePaginator;

    public function hasProducts(Category $category): bool;
}
