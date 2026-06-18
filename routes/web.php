<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LowStockAlertController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::middleware('role:administrador')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    });

    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware('role:administrador')->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/adjust', [ProductController::class, 'adjust'])
            ->name('products.adjust');
    });

    Route::resource('stock-movements', StockMovementController::class)
        ->only(['index', 'create', 'store']);

    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/entries', [ReportController::class, 'entries'])->name('reports.entries');
    Route::get('reports/exits', [ReportController::class, 'exits'])->name('reports.exits');
    Route::get('reports/entries/export/{format}', [ReportController::class, 'exportEntries'])->name('reports.entries.export');
    Route::get('reports/exits/export/{format}', [ReportController::class, 'exportExits'])->name('reports.exits.export');

    Route::middleware('role:administrador')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::get('low-stock-alerts', [LowStockAlertController::class, 'index'])->name('low-stock-alerts.index');
        Route::patch('low-stock-alerts/{alert}/resolve', [LowStockAlertController::class, 'resolve'])
            ->name('low-stock-alerts.resolve');

        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
