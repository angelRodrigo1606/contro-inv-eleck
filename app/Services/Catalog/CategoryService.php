<?php

namespace App\Services\Catalog;

use App\Dtos\Data\CategoryData;
use App\Dtos\Input\StoreCategoryData;
use App\Dtos\Input\UpdateCategoryData;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Exceptions\DependencyException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function create(StoreCategoryData $data): CategoryData
    {
        return $this->categoryRepository->create($data);
    }

    public function update(int|string $id, UpdateCategoryData $data): CategoryData
    {
        return $this->categoryRepository->update($id, $data);
    }

    /**
     * @throws DependencyException
     */
    public function delete(int|string $id): void
    {
        if ($this->categoryRepository->hasProducts($id)) {
            throw new DependencyException('la categoría');
        }

        $this->categoryRepository->delete($id);
    }
}
