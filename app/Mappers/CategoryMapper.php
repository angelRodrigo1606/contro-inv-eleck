<?php

namespace App\Mappers;

use App\Dtos\Data\CategoryData;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryMapper
{
    public static function toData(Category $category): CategoryData
    {
        return new CategoryData(
            id: $category->id,
            name: $category->name,
            description: $category->description,
            isActive: (bool) $category->is_active,
            productsCount: $category->products_count !== null ? (int) $category->products_count : 0,
            createdAt: $category->created_at?->toDateTimeImmutable(),
            updatedAt: $category->updated_at?->toDateTimeImmutable(),
            deletedAt: $category->deleted_at?->toDateTimeImmutable(),
        );
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, CategoryData>
     */
    public static function toDataCollection(Collection $categories): Collection
    {
        return $categories->map(fn (Category $category) => self::toData($category));
    }
}
