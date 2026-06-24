<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\StockMovementData;
use App\Dtos\Input\RegisterStockMovementData;
use App\Dtos\PaginatedData;
use App\Mappers\StockMovementMapper;
use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Support\Collection;
use stdClass;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }

    public function search(array $filters): PaginatedData
    {
        $paginator = StockMovement::with(['product.category', 'user'])
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['product_id'] ?? null, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($filters['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return PaginatedData::fromLengthAwarePaginator($paginator, [StockMovementMapper::class, 'toData']);
    }

    public function getByType(array $filters, string $type, bool $paginate = true): PaginatedData|Collection
    {
        $query = StockMovement::with(['product.category', 'product.supplier', 'user'])
            ->where('type', $type)
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['product_id'] ?? null, fn ($query, $id) => $query->where('product_id', $id))
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->whereHas('product', fn ($subQuery) => $subQuery->where('category_id', $categoryId));
            })
            ->when($filters['supplier_id'] ?? null, function ($query, $supplierId) {
                $query->whereHas('product', fn ($subQuery) => $subQuery->where('supplier_id', $supplierId));
            })
            ->orderByDesc('created_at');

        if (! $paginate) {
            return StockMovementMapper::toDataCollection($query->get());
        }

        $paginator = $query->paginate(20)->withQueryString();

        return PaginatedData::fromLengthAwarePaginator($paginator, [StockMovementMapper::class, 'toData']);
    }

    public function createForProduct(int|string $productId, RegisterStockMovementData $data): StockMovementData
    {
        $movement = StockMovement::create([
            'product_id' => $productId,
            'user_id' => $data->userId,
            'type' => $data->type,
            'quantity' => $data->quantity,
            'reference' => $data->reference,
            'notes' => $data->notes,
        ]);

        return StockMovementMapper::toData($movement);
    }

    public function recent(int $limit = 10): Collection
    {
        return StockMovementMapper::toDataCollection(
            StockMovement::with(['product', 'user'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
        );
    }

    public function monthlySummary(int $months = 12): Collection
    {
        $startDate = now()->subMonths($months - 1)->startOfMonth();

        $movements = StockMovement::where('created_at', '>=', $startDate)
            ->whereIn('type', ['entry', 'exit'])
            ->get()
            ->groupBy(fn (StockMovement $movement) => $movement->created_at->format('Y-m'))
            ->map(fn (Collection $group) => [
                'entries' => $group->where('type', 'entry')->sum('quantity'),
                'exits' => $group->where('type', 'exit')->sum('quantity'),
            ]);

        $summary = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthData = $movements->get($month, ['entries' => 0, 'exits' => 0]);

            $item = new stdClass;
            $item->month = $month;
            $item->entries = (int) $monthData['entries'];
            $item->exits = (int) $monthData['exits'];

            $summary->push($item);
        }

        return $summary;
    }
}
