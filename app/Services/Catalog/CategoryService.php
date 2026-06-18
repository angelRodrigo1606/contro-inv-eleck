<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Services\Exceptions\DependencyException;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    /**
     * @throws DependencyException
     */
    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            throw new DependencyException('la categoría');
        }

        $category->delete();
    }
}
