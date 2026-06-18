<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

describe('products', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
    });

    it('allows authenticated users to view products list', function () {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->employee)->get(route('products.index'));

        $response->assertOk()->assertSee('Listado de productos');
    });

    it('allows admin to create a product with initial stock movement', function () {
        $data = [
            'name' => 'Resistencia 1k',
            'sku' => 'RES-001',
            'description' => 'Resistencia de 1k ohm',
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'price' => 0.50,
            'quantity' => 100,
            'min_stock' => 20,
        ];

        $response = $this->actingAs($this->admin)->post(route('products.store'), $data);

        $response->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['sku' => 'RES-001', 'quantity' => 100]);
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'entry',
            'quantity' => 100,
            'reference' => 'Inventario inicial',
        ]);
    });

    it('allows admin to update a product except sku', function () {
        $product = Product::factory()->create();
        $oldSku = $product->sku;

        $response = $this->actingAs($this->admin)
            ->put(route('products.update', $product), [
                'name' => 'Nombre actualizado',
                'description' => $product->description,
                'category_id' => $product->category_id,
                'supplier_id' => $product->supplier_id,
                'price' => $product->price,
                'min_stock' => $product->min_stock,
            ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nombre actualizado', 'sku' => $oldSku]);
    });

    it('allows admin to adjust stock manually', function () {
        $product = Product::factory()->create(['quantity' => 100]);

        $response = $this->actingAs($this->admin)
            ->post(route('products.adjust', $product), [
                'quantity' => 90,
                'reason' => 'Ajuste por inventario',
            ]);

        $response->assertRedirect(route('products.show', $product));
        $product->refresh();
        expect($product->quantity)->toBe(90);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => -10,
        ]);
    });

    it('forbids employee to create a product', function () {
        $data = [
            'name' => 'Producto no permitido',
            'sku' => 'NOP-001',
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'price' => 10,
            'quantity' => 10,
            'min_stock' => 1,
        ];

        $response = $this->actingAs($this->employee)->post(route('products.store'), $data);

        $response->assertForbidden();
    });

    it('highlights low stock products', function () {
        Product::factory()->create(['quantity' => 3, 'min_stock' => 5]);

        $response = $this->actingAs($this->employee)->get(route('products.index'));

        $response->assertOk()->assertSee('bg-red-50');
    });
});
