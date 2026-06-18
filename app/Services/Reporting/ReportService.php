<?php

namespace App\Services\Reporting;

use App\Models\Category;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardData(): array
    {
        return [
            'totalValue' => Product::sum(DB::raw('quantity * price')),
            'lowStockCount' => Product::lowStock()->count(),
            'recentMovements' => StockMovement::with(['product', 'user'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'unresolvedAlerts' => LowStockAlert::with('product')
                ->unresolved()
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];
    }

    public function getMovements(array $filters, string $type, bool $paginate = true): LengthAwarePaginator|Collection
    {
        $query = StockMovement::with(['product.category', 'product.supplier', 'user'])
            ->where('type', $type)
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($filters['product_id'] ?? null, fn ($q, $id) => $q->where('product_id', $id))
            ->when($filters['category_id'] ?? null, function ($q, $categoryId) {
                $q->whereHas('product', fn ($sq) => $sq->where('category_id', $categoryId));
            })
            ->when($filters['supplier_id'] ?? null, function ($q, $supplierId) {
                $q->whereHas('product', fn ($sq) => $sq->where('supplier_id', $supplierId));
            })
            ->orderByDesc('created_at');

        return $paginate
            ? $query->paginate(20)->withQueryString()
            : $query->get();
    }

    public function buildExportFilename(string $type): string
    {
        $label = $type === 'entry' ? 'entradas' : 'salidas';

        return "reporte-{$label}-".now()->format('Y-m-d');
    }

    public function getFilterOptions(): array
    {
        return [
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ];
    }
}
