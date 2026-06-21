<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function allOrdered(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function paginateWithProductCount(int $perPage = 15): LengthAwarePaginator
    {
        return Category::orderBy('name')
            ->withCount('products')
            ->paginate($perPage);
    }

    public function hasProducts(Category $category): bool
    {
        return $category->products()->exists();
    }
}
