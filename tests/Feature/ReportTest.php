<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;

describe('reports', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
        $this->product = Product::factory()->create();
        StockMovement::factory()->count(3)->create([
            'product_id' => $this->product->id,
            'type' => 'entry',
            'user_id' => $this->employee->id,
        ]);
        StockMovement::factory()->count(2)->create([
            'product_id' => $this->product->id,
            'type' => 'exit',
            'user_id' => $this->employee->id,
        ]);
    });

    it('allows authenticated users to view entries report', function () {
        $response = $this->actingAs($this->employee)->get(route('reports.entries'));
        $response->assertOk()->assertSee('Entradas de mercancía');
    });

    it('allows authenticated users to view exits report', function () {
        $response = $this->actingAs($this->employee)->get(route('reports.exits'));
        $response->assertOk()->assertSee('Salidas de mercancía');
    });

    it('filters entries by product', function () {
        $otherProduct = Product::factory()->create();
        StockMovement::factory()->create(['product_id' => $otherProduct->id, 'type' => 'entry']);

        $response = $this->actingAs($this->admin)
            ->get(route('reports.entries', ['product_id' => $this->product->id]));

        $response->assertOk();
    });

    it('exports entries as csv', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.entries.export', ['format' => 'csv']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    });

    it('exports entries as pdf', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.entries.export', ['format' => 'pdf']));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    });
});
