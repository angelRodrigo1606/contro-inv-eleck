<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function dashboard(): View
    {
        $totalValue = Product::sum(DB::raw('quantity * price'));
        $lowStockCount = Product::lowStock()->count();
        $recentMovements = StockMovement::with(['product', 'user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $unresolvedAlerts = LowStockAlert::with('product')
            ->unresolved()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact('totalValue', 'lowStockCount', 'recentMovements', 'unresolvedAlerts'));
    }

    public function entries(Request $request): View
    {
        $movements = $this->queryMovements($request, 'entry');
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('reports.entries', compact('movements', 'products', 'categories', 'suppliers'));
    }

    public function exits(Request $request): View
    {
        $movements = $this->queryMovements($request, 'exit');
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('reports.exits', compact('movements', 'products', 'categories', 'suppliers'));
    }

    public function exportEntries(Request $request, string $format)
    {
        $movements = $this->queryMovements($request, 'entry', false);
        $filename = 'reporte-entradas-'.now()->format('Y-m-d');

        return $this->export($movements, $format, $filename, 'entry');
    }

    public function exportExits(Request $request, string $format)
    {
        $movements = $this->queryMovements($request, 'exit', false);
        $filename = 'reporte-salidas-'.now()->format('Y-m-d');

        return $this->export($movements, $format, $filename, 'exit');
    }

    private function queryMovements(Request $request, string $type, bool $paginate = true)
    {
        $query = StockMovement::with(['product.category', 'product.supplier', 'user'])
            ->where('type', $type)
            ->when($request->from, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($request->to, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($request->product_id, fn ($q, $id) => $q->where('product_id', $id))
            ->when($request->category_id, function ($q, $categoryId) {
                $q->whereHas('product', fn ($sq) => $sq->where('category_id', $categoryId));
            })
            ->when($request->supplier_id, function ($q, $supplierId) {
                $q->whereHas('product', fn ($sq) => $sq->where('supplier_id', $supplierId));
            })
            ->orderByDesc('created_at');

        return $paginate ? $query->paginate(20)->withQueryString() : $query->get();
    }

    private function export($movements, string $format, string $filename, string $type)
    {
        $format = strtolower($format);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('movements', 'type'));

            return $pdf->download($filename.'.pdf');
        }

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            ];

            $columns = $type === 'entry'
                ? ['Fecha', 'Producto', 'Categoría', 'Cantidad', 'Proveedor', 'Usuario', 'Referencia']
                : ['Fecha', 'Producto', 'Categoría', 'Cantidad', 'Usuario', 'Referencia'];

            return new StreamedResponse(function () use ($movements, $columns) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $columns);

                foreach ($movements as $movement) {
                    $row = [
                        $movement->created_at->format('d/m/Y H:i'),
                        $movement->product->name,
                        $movement->product->category->name,
                        $movement->quantity,
                    ];

                    if ($movement->type === 'entry') {
                        $row[] = $movement->product->supplier->name;
                    }

                    $row[] = $movement->user->name;
                    $row[] = $movement->reference;

                    fputcsv($handle, $row);
                }

                fclose($handle);
            }, 200, $headers);
        }

        abort(404, 'Formato no soportado');
    }
}
