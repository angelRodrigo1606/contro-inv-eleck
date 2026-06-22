<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\CategoryData;
use App\Dtos\Input\StoreCategoryData;
use App\Dtos\Input\UpdateCategoryData;
use App\Dtos\PaginatedData;
use App\Mappers\CategoryMapper;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return CategoryMapper::toDataCollection($this->model->all());
    }

    public function find(int|string $id): ?CategoryData
    {
        $category = $this->doFind($id);

        return $category ? CategoryMapper::toData($category) : null;
    }

    public function findOrFail(int|string $id): CategoryData
    {
        return CategoryMapper::toData($this->doFindOrFail($id));
    }

    public function create(StoreCategoryData $data): CategoryData
    {
        $category = $this->doCreate([
            'name' => $data->name,
            'description' => $data->description,
            'is_active' => $data->isActive,
        ]);

        return CategoryMapper::toData($category);
    }

    public function update(int|string $id, UpdateCategoryData $data): CategoryData
    {
        $category = $this->doUpdate($id, [
            'name' => $data->name,
            'description' => $data->description,
            'is_active' => $data->isActive,
        ]);

        return CategoryMapper::toData($category);
    }

    public function delete(int|string $id): void
    {
        $this->doDelete($id);
    }

    public function paginateWithProductCount(int $perPage = 15): PaginatedData
    {
        $paginator = Category::orderBy('name')
            ->withCount('products')
            ->paginate($perPage);

        return PaginatedData::fromLengthAwarePaginator($paginator, [CategoryMapper::class, 'toData']);
    }

    public function allOrdered(): Collection
    {
        return CategoryMapper::toDataCollection(
            Category::orderBy('name')->get()
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
