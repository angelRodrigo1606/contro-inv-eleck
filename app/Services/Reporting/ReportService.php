<?php

namespace App\Services\Reporting;

use App\Dtos\PaginatedData;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\LowStockAlertRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private StockMovementRepositoryInterface $stockMovementRepository,
        private LowStockAlertRepositoryInterface $lowStockAlertRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function getDashboardData(): array
    {
        return [
            'totalValue' => $this->productRepository->sumTotalValue(),
            'lowStockCount' => $this->productRepository->lowStockCount(),
            'recentMovements' => $this->stockMovementRepository->recent(),
            'unresolvedAlerts' => $this->lowStockAlertRepository->recentUnresolved(),
        ];
    }

    public function getMovements(array $filters, string $type, bool $paginate = true): PaginatedData|Collection
    {
        return $this->stockMovementRepository->getByType($filters, $type, $paginate);
    }

    public function buildExportFilename(string $type): string
    {
        $label = $type === 'entry' ? 'entradas' : 'salidas';

        return "reporte-{$label}-".now()->format('Y-m-d');
    }

    public function getFilterOptions(): array
    {
        return [
            'products' => $this->productRepository->allOrdered(),
            'categories' => $this->categoryRepository->allOrdered(),
            'suppliers' => $this->supplierRepository->allOrdered(),
        ];
    }
}
