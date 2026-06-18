<?php

use App\Models\Product;
use App\Models\User;

describe('stock movements', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
        $this->product = Product::factory()->create(['quantity' => 50]);
    });

    it('allows authenticated users to view movements', function () {
        $response = $this->actingAs($this->employee)->get(route('stock-movements.index'));
        $response->assertOk();
    });

    it('increments stock on entry', function () {
        $data = [
            'product_id' => $this->product->id,
            'type' => 'entry',
            'quantity' => 20,
            'reference' => 'Factura 123',
        ];

        $response = $this->actingAs($this->employee)->post(route('stock-movements.store'), $data);

        $response->assertRedirect(route('stock-movements.index'));
        $this->product->refresh();
        expect($this->product->quantity)->toBe(70);
    });

    it('decrements stock on exit', function () {
        $data = [
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 10,
            'reference' => 'Venta 456',
        ];

        $response = $this->actingAs($this->employee)->post(route('stock-movements.store'), $data);

        $response->assertRedirect(route('stock-movements.index'));
        $this->product->refresh();
        expect($this->product->quantity)->toBe(40);
    });

    it('fails when exit quantity exceeds stock', function () {
        $data = [
            'product_id' => $this->product->id,
            'type' => 'exit',
            'quantity' => 100,
        ];

        $response = $this->actingAs($this->employee)->post(route('stock-movements.store'), $data);

        $response->assertSessionHas('error');
        $this->product->refresh();
        expect($this->product->quantity)->toBe(50);
    });

    it('rejects zero or negative quantity', function () {
        $response = $this->actingAs($this->employee)->post(route('stock-movements.store'), [
            'product_id' => $this->product->id,
            'type' => 'entry',
            'quantity' => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    });
});
