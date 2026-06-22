<?php

use App\Mappers\ProductMapper;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

it('maps a product to ProductData', function () {
    $category = new Category(['name' => 'Category', 'is_active' => true]);
    $category->id = 1;
    $supplier = new Supplier(['name' => 'Supplier', 'is_active' => true]);
    $supplier->id = 2;

    $product = new Product([
        'name' => 'Mouse',
        'sku' => 'MOU-001',
        'price' => 25.50,
        'quantity' => 15,
        'min_stock' => 5,
    ]);
    $product->id = 10;
    $product->setRelation('category', $category);
    $product->setRelation('supplier', $supplier);

    $data = ProductMapper::toData($product);

    expect($data->id)->toBe(10)
        ->and($data->name)->toBe('Mouse')
        ->and($data->sku)->toBe('MOU-001')
        ->and($data->price)->toBe(25.50)
        ->and($data->quantity)->toBe(15)
        ->and($data->minStock)->toBe(5)
        ->and($data->isLowStock)->toBeFalse()
        ->and($data->category)->not->toBeNull()
        ->and($data->supplier)->not->toBeNull();
});

it('marks product as low stock when quantity equals min stock', function () {
    $product = new Product([
        'name' => 'Low Stock Item',
        'sku' => 'LOW-001',
        'price' => 10.00,
        'quantity' => 3,
        'min_stock' => 3,
    ]);
    $product->id = 20;

    $data = ProductMapper::toData($product);

    expect($data->isLowStock)->toBeTrue();
});
