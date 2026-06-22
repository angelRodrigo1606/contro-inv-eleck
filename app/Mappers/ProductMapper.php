<?php

namespace App\Mappers;

use App\Dtos\Data\ProductData;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductMapper
{
    public static function toData(Product $product, bool $withStockMovements = true): ProductData
    {
        return new ProductData(
            id: $product->id,
            name: $product->name,
            description: $product->description,
            sku: $product->sku,
            price: (float) $product->price,
            quantity: (int) $product->quantity,
            minStock: (int) $product->min_stock,
            isLowStock: $product->isLowStock(),
            category: $product->relationLoaded('category') && $product->category ? CategoryMapper::toData($product->category) : null,
            supplier: $product->relationLoaded('supplier') && $product->supplier ? SupplierMapper::toData($product->supplier) : null,
            stockMovements: $withStockMovements && $product->relationLoaded('stockMovements')
                ? StockMovementMapper::toDataCollection($product->stockMovements)
                : new Collection,
            createdAt: $product->created_at?->toDateTimeImmutable(),
            updatedAt: $product->updated_at?->toDateTimeImmutable(),
            deletedAt: $product->deleted_at?->toDateTimeImmutable(),
        );
    }

    public static function toBasicData(Product $product): ProductData
    {
        return self::toData($product, withStockMovements: false);
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return Collection<int, ProductData>
     */
    public static function toDataCollection(Collection $products, bool $withStockMovements = false): Collection
    {
        return $products->map(fn (Product $product) => self::toData($product, $withStockMovements));
    }
}
