<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Exceptions\DependencyException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    /**
     * @throws DependencyException
     */
    public function delete(Category $category): void
    {
        if ($this->categoryRepository->hasProducts($category)) {
            throw new DependencyException('la categoría');
        }

        $this->categoryRepository->delete($category);
    }
}
