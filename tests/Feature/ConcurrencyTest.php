<?php

use App\Dtos\Input\AdjustStockData;
use App\Dtos\Input\RegisterStockMovementData;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use App\Services\Exceptions\InsufficientStockException;
use App\Services\Inventory\ProductService;
use App\Services\Inventory\StockAlertService;
use App\Services\Inventory\StockMovementService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

describe('concurrency safeguards', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
    });

    it('does not allow stock to become negative through sequential exits', function () {
        $product = Product::factory()->create(['quantity' => 10, 'min_stock' => 0]);
        $service = app(StockMovementService::class);

        $successful = 0;
        $failed = 0;

        foreach (range(1, 3) as $i) {
            try {
                $service->register(new RegisterStockMovementData(
                    productId: $product->id,
                    type: 'exit',
                    quantity: 5,
                    reference: 'Salida '.$i,
                    notes: null,
                ), $this->employee->id);
                $successful++;
            } catch (InsufficientStockException $e) {
                $failed++;
            }
        }

        $product->refresh();

        expect($successful)->toBe(2)
            ->and($failed)->toBe(1)
            ->and($product->quantity)->toBe(0);
    });

    it('uses row lock when reading product for stock movement', function () {
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not emit FOR UPDATE syntax for lockForUpdate().');
        }

        $product = Product::factory()->create(['quantity' => 10]);
        $service = app(StockMovementService::class);

        DB::enableQueryLog();

        $service->register(new RegisterStockMovementData(
            productId: $product->id,
            type: 'entry',
            quantity: 5,
            reference: 'Entrada con lock',
            notes: null,
        ), $this->employee->id);

        $queries = collect(DB::getQueryLog())->pluck('query')->implode(' ');

        expect($queries)->toContain('for update')
            ->or($queries)->toContain('FOR UPDATE');
    });

    it('creates only one unresolved alert per product', function () {
        $product = Product::factory()->create(['quantity' => 5, 'min_stock' => 10]);

        $service = app(StockAlertService::class);

        $service->syncForProduct($product->id);
        $service->syncForProduct($product->id);
        $service->syncForProduct($product->id);

        expect(LowStockAlert::where('product_id', $product->id)->unresolved()->count())->toBe(1);
    });

    it('resolves alert when stock rises above threshold', function () {
        $product = Product::factory()->create(['quantity' => 5, 'min_stock' => 10]);

        $service = app(StockAlertService::class);
        $service->syncForProduct($product->id);

        expect(LowStockAlert::where('product_id', $product->id)->unresolved()->count())->toBe(1);

        $product->update(['quantity' => 20]);
        $service->syncForProduct($product->id);

        expect(LowStockAlert::where('product_id', $product->id)->unresolved()->count())->toBe(0);
    });

    it('prevents negative stock at the database level', function () {
        $product = Product::factory()->create(['quantity' => 0]);

        expect(fn () => $product->update(['quantity' => -1]))
            ->toThrow(QueryException::class);
    });

    it('prevents concurrent manual adjustments from losing updates', function () {
        $product = Product::factory()->create(['quantity' => 10]);
        $service = app(ProductService::class);

        $service->adjustStock(
            $product->id,
            new AdjustStockData(quantity: 15, reason: 'Ajuste A'),
            $this->admin->id
        );

        $service->adjustStock(
            $product->id,
            new AdjustStockData(quantity: 8, reason: 'Ajuste B'),
            $this->admin->id
        );

        $product->refresh();

        expect($product->quantity)->toBe(8);
    });
});
