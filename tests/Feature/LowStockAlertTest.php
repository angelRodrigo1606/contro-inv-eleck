<?php

use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\User;

describe('low stock alerts', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
        $this->product = Product::factory()->create(['quantity' => 50, 'min_stock' => 10]);
    });

    it('creates an alert when stock goes below minimum', function () {
        $this->actingAs($this->employee)
            ->post(route('stock-movements.store'), [
                'product_id' => $this->product->id,
                'type' => 'exit',
                'quantity' => 45,
            ]);

        $this->product->refresh();
        expect($this->product->quantity)->toBe(5);
        $this->assertDatabaseHas('low_stock_alerts', [
            'product_id' => $this->product->id,
            'resolved_at' => null,
        ]);
    });

    it('resolves alert when stock goes above minimum', function () {
        LowStockAlert::create(['product_id' => $this->product->id]);

        $this->actingAs($this->employee)
            ->post(route('stock-movements.store'), [
                'product_id' => $this->product->id,
                'type' => 'entry',
                'quantity' => 100,
            ]);

        $this->assertDatabaseMissing('low_stock_alerts', [
            'product_id' => $this->product->id,
            'resolved_at' => null,
        ]);
    });

    it('allows admin to view alerts', function () {
        LowStockAlert::create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->admin)->get(route('low-stock-alerts.index'));

        $response->assertOk()->assertSee('Alertas de stock bajo');
    });

    it('forbids employee to view alerts', function () {
        $response = $this->actingAs($this->employee)->get(route('low-stock-alerts.index'));

        $response->assertForbidden();
    });
});
