<?php

use App\Dtos\Data\CategoryData;
use App\Mappers\CategoryMapper;
use App\Models\Category;

it('maps a category to CategoryData', function () {
    $category = new Category([
        'name' => 'Electronics',
        'description' => 'Electronic items',
        'is_active' => true,
    ]);
    $category->id = 1;

    $data = CategoryMapper::toData($category);

    expect($data->id)->toBe(1)
        ->and($data->name)->toBe('Electronics')
        ->and($data->description)->toBe('Electronic items')
        ->and($data->isActive)->toBeTrue();
});

it('maps a collection of categories', function () {
    $first = new Category(['name' => 'A', 'is_active' => true]);
    $first->id = 1;
    $second = new Category(['name' => 'B', 'is_active' => false]);
    $second->id = 2;

    $collection = CategoryMapper::toDataCollection(collect([$first, $second]));

    expect($collection)->toHaveCount(2)
        ->and($collection->first())->toBeInstanceOf(CategoryData::class);
});
